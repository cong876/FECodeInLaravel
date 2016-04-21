<?php


namespace App\Http\ApiControllers\UserSubOrder;

use App\Helper\WXNotice;
use App\Models\GroupItem;
use App\Models\SubOrderMemo;
use App\Repositories\GroupItem\GroupItemRepositoryInterface;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\SubOrder;
use App\Models\User;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Utils\Json\ResponseTrait;
use App\Http\ApiControllers\Controller;
use App\Transforms\MyOrderWxWaitPaySubOrderTransFormer;
use App\Transforms\MyOrderWxAfterPaidSubOrderTransformer;
use Illuminate\Support\Facades\Cache;


class UserSubOrderController extends Controller
{
    use ResponseTrait;

    private $subOrder, $mainOrder, $groupItem;

    public function __construct(SubOrderRepositoryInterface $subOrder,
                                MainOrderRepositoryInterface $mainOrder,
                                GroupItemRepositoryInterface $groupItem)
    {
        $this->subOrder = $subOrder;
        $this->mainOrder = $mainOrder;
        $this->groupItem = $groupItem;
    }

    public function index($user, $state)
    {
        $query = SubOrder::where('buyer_id', $user);

        switch ($state) {
            case 'waitPay':
                $query->canPay()->orderBy('updated_at', 'desc');
                return $this->response->collection($query->get(), new MyOrderWxWaitPaySubOrderTransFormer);
                break;
            case 'waitDelivery':
                $query->needToDeliver()->orderBy('updated_at', 'desc');
                break;
            case 'delivered':
                $query->delivered()->orderBy('updated_at', 'desc');
                break;
            case 'completed':
                $query->hasFinished()->orderBy('updated_at', 'desc');
                $paged = $query->paginate(10);
                $path = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('user.suborders', [
                    'user' => $user,
                    'state' => $state
                ]);
                $paged->setPath($path);
                return $this->response->paginator($paged, new MyOrderWxAfterPaidSubOrderTransformer);
                break;
        }

        return $this->response->collection($query->get(), new MyOrderWxAfterPaidSubOrderTransformer);
    }

    public function delete($user_id, $suborder_id)
    {
        $suborder = SubOrder::find($suborder_id);
        if ($suborder->buyer_id != $user_id) {
            $ret = $this->requestFailed(400, "用户ID与订单用户不匹配");
            return $this->response->array($ret);
        }

        $buyer_openid = User::find($user_id)->openid;
        $title = '';
        $items = $suborder->items;
        $price = 0;
        DB::beginTransaction();
        $itemSaved = true;
        $skuSaved = true;
        foreach ($items as $item) {
            $title .= $item->title . '；';
            $item_count = $item->item_type == 1 ?
                $item->detail_positive->number :
                GroupItem::where('sub_order_id', $suborder->sub_order_id)->first()->number;
            $price += $item->price * $item_count;
            if ($suborder->order_type == 0) {
                $item->is_available = 0;
                $itemSaved = $item->save();
            }
            // 回补库存策略
            if ($suborder->order_type == 1 || $suborder->order_type == 3 || $suborder->order_type == 4) {
                $sku = $item->skus->first();
                $sku->sku_inventory += $item_count;
                $skuSaved = $sku->save();
            }
        }
        $suborderDeleted = $this->subOrder->deleteSubOrderByUser($suborder);
        $price += $suborder->postage;

        if ($itemSaved && $skuSaved && $suborderDeleted) {
            DB::commit();
            $ret = $this->requestSucceed();
            if (mb_strlen($title) > 12) {
                $title = mb_substr($title, 0, 12) . '...';
            } else {
                $title = rtrim($title, '；');
            }
            if ($suborder->buyer->is_subscribed == 1) {
                $notice = new WXNotice();
                $notice->orderCanceled($buyer_openid, $suborder->sub_order_number, $title, sprintf('%.2f', $price));

            }
            return $this->response->array($ret);
        } else {
            DB::rollback();
            $ret = $this->requestFailed(400, "订单取消失败,请重试");
            return $this->response->array($ret);
        }
    }

    public function hide($user_id, $suborder_id)
    {
        $suborder = SubOrder::find($suborder_id);
        if ($suborder->buyer_id != $user_id) {
            $ret = $this->requestFailed(400, "用户ID与订单用户不匹配");
            return $this->response->array($ret);
        }

        $hided = $this->subOrder->hideFinishedSubOrder($suborder);
        if ($hided) {
            $ret = $this->requestSucceed();
            return $this->response->array($ret);
        } else {
            $ret = $this->requestFailed(400, "已完成订单删除失败");
            return $this->response->array($ret);
        }

    }

    public function received($user_id, $suborder_id)
    {
        $suborder = SubOrder::find($suborder_id);
        if ($suborder->buyer_id != $user_id) {
            $ret = $this->requestFailed(400, "用户ID与订单用户不匹配");
            return $this->response->array($ret);
        }

        $suborder->sub_order_state = 301;
        $suborder->completed_time = date('Y-m-d H:i:s');
        if ($suborder->transfer_price == 0) {
            $seller_id = $suborder->seller_id;
            $seller = Seller::find($seller_id);
            $seller->seller_success_orders_num += 1;
            $suborder->transfer_price = $suborder->sub_order_price - $suborder->refund_price;
            $seller->seller_success_incoming += $suborder->transfer_price;
            $suborder->audit_passed_time = date('Y-m-d H:i:s');
            $suborder->completed_time = date('Y-m-d H:i:s');
            $seller->save();
        }
        $suborder->save();
        $buyer = Buyer::where('hlj_id', $user_id)->first();
        $buyer->buyer_success_orders_num += 1;
        $buyer->buyer_actual_paid += $suborder->sub_order_price - $suborder->refund_price;
        $buyer->save();

        $ret = $this->requestSucceed();
        return $this->response->array($ret);
    }

    /**
     * 团购,福袋,秒杀下单接口
     *
     * @param Request $request
     * @param $user_id
     * @return mixed
     */
    public function store(Request $request, $user_id)
    {
        // 限制机器重复提交
        $key = md5('User:' . $user_id . ':GroupPurchasing:' . json_encode($request->get('item_id')));
        if (Cache::get($key)) {
            $ret = $this->requestFailed(420, "请勿频繁提交订单");
            return $this->response->array($ret);
        } else {
            $expiresAt = Carbon::now()->addMinute(1);
            Cache::put($key, 1, $expiresAt);
        }

        // 获取登陆用户和请求数据
        $user = DB::table('users')->where('hlj_id', $user_id)->first();
        $successSecTokenKey = "User:".$user->hlj_id.":GetSecKillRecently";
        $groupMemo = '';
        if ($item_id = $request->item_id) {
            // 保存查询句柄
            $itemQuery = DB::table('items')->where('item_id', $item_id);
            $skuQuery = DB::table('skus')->where('item_id', $item_id);

            // 通过sharedLock获得sku & item,在sku | item更新时该语句会被挂起,之后保证得到的sku & item为最新值
            $item = $itemQuery->sharedLock()->first();
            $sku = $skuQuery->sharedLock()->first();
        } else {
            $response = $this->requestFailed(430, "未包含商品信息");
            return $this->response()->array($response);
        }
        $sub_order_type = $item->item_type - 1;
        $number = $request->number ?? 1;

        // 获得交易价格: 商品单价 * 数量 + 邮费
        $metaData = json_decode($item->attributes);
        $order_info = json_decode($metaData)->activity_meta;
        $postage = $order_info->postage;
        $price = $item->price * $number + $postage * $number;

        //避免出现一人多次购买福袋情况
        if ($sub_order_type == 3) {
            $luckBagCount = DB::table('sub_orders')->where('buyer_id', $user_id)
                ->where('order_type', 3)
                ->where('sub_order_state', 201)
                ->count();
            if ($luckBagCount > 0) {
                $response = $this->requestFailed(440, "不符合购买福袋条件");
                return $this->response()->array($response);
            }
        }

        // 秒杀黑名单防止机器刷
        if (Cache::get($successSecTokenKey) && $sub_order_type == 4) {
            $response = $this->requestFailed(400, "已抢光,提前设置提醒不错过");
            return $this->response()->array($response);
        }

        // 团购秒杀福袋判断
        if ($sub_order_type == 4 || $sub_order_type == 1 || $sub_order_type == 3) {
            if (!$item->is_on_shelf || $sku->sku_inventory <= 0) {
                switch ($sub_order_type) {
                    case 1:
                    case 3:
                        $response = $this->requestFailed(400, "商品已售罄,请尝试点击帮我代购");
                        break;
                    case 4:
                    default:
                        $response = $this->requestFailed(400, "已抢光,提前设置提醒不错过");
                        break;
                }
                return $this->response()->array($response);
            }
            // 未注册不能参加福袋,秒杀,团购
            if ($user->is_subscribed == 0) {
                $response = $this->requestFailed(403, "请先关注红领巾小助手公众号");
                return $this->response()->array($response);
            }
            if ($sku->sku_inventory < $number) {
                $response = $this->requestFailed(450, "库存不足,您只能购买{$sku->sku_inventory}件");
                return $this->response()->array($response);
            }
            if ($number > $item->buy_per_user) {
                $response = $this->requestFailed(460, "超过每人限购量,无法购买");
                return $this->response()->array($response);
            }
            $groupMemo = $request->order_memo ? $request->order_memo : '';
        }

        // 不同清除缓存策略,清缓存动作不在事务中执行
        $needClearCache = false;

        DB::beginTransaction(); // 开启事务，生成相关订单，处理相关数据

        // 拍下减库存策略
        $skuQuery->lockForUpdate()->get();
        $itemQuery->lockForUpdate()->first();

        $sku_updated = $skuQuery->decrement('sku_inventory', $number);
        $updatedSku = $skuQuery->first();
        $item_updated = true;
        if ($updatedSku->sku_inventory == 0) {
            // 下架商品,调用清除缓存策略
            $item_updated = $itemQuery->update(['is_on_shelf' => 0]);
            $needClearCache = true;
        }
        $createdMainOrder = $this->mainOrder->createMainOrder(
            array('main_order_state' => 301, 'main_order_price' => $price),
            $user->hlj_id
        );

        $createdMainOrder->items()->attach($item->item_id);

        // 获取活动相关信息
        $seller_id = $order_info->seller_id;
        $postage = $order_info->postage;
        $operator_id = $order_info->operator_id;

        // 创建待付款子订单
        $createdSuborder = $this->subOrder->createSubOrder(
            [
                'main_order_id' => $createdMainOrder->main_order_id, 'buyer_id' => $user->hlj_id,
                'postage' => $postage * $number, 'sub_order_price' => $price,
                'country_id' => $item->country_id, 'seller_id' => $seller_id,
                'operator_id' => $operator_id, 'order_type' => $sub_order_type,
                'sub_order_state' => 201, 'created_offer_time' => date('Y-m-d H:i:s'),
            ]
        );

        $createdSuborder->items()->attach($item->item_id);

        // 记录用户子订单及其关联的商品&数量
        $info = $this->groupItem->createGroupItem(
            [
                'item_id' => $item->item_id, 'sub_order_id' => $createdSuborder->sub_order_id,
                'number' => $number, 'hlj_id' => $user->hlj_id,
                'memo' => $groupMemo
            ]
        );
        $this->subOrder->createOrUpdateSubOrderBidSnapshot($createdSuborder);

        if (strlen($groupMemo) > 0) {
            SubOrderMemo::create(
                ['content' => $groupMemo, 'hlj_id' => rand(1, 9),
                    'sub_order_id' => $createdSuborder->sub_order_id
                ]
            );
        }

        // 尝试解决多卖问题
        $sku_checking = $skuQuery->first();
        if ($sku_checking->sku_inventory < 0) {
            $inventoryReCheckedOK = false;
        } else {
            $inventoryReCheckedOK = true;
        }


        if ($sku_updated && $createdMainOrder && $createdSuborder && $info && $item_updated && $inventoryReCheckedOK) {
            DB::commit();
            $response = ['data' => ['id' => $createdSuborder->sub_order_id],
                'status_code' => '200',
                'message' => 'OK'];
            // 限制一个月内不能重复购买
            if ($sub_order_type == 4) {
                $expiresAt = Carbon::now()->addDays(30);
                Cache::put($successSecTokenKey, true, $expiresAt);
            }
            // 团购,秒杀使用不同清除缓存策略
            if ($needClearCache) {
                switch ($sub_order_type) {
                    case 1:
                        $activity_item = DB::table('activity_item')->where('item_id', $item->item_id)->first();
                        Cache::forget('PeriodActivity:' . $activity_item->activity_id);
                        Cache::forget('ActivityInfo:' . $activity_item->activity_id);
                        break;
                }
            }
        } else {
            DB::rollback();
            $response = $this->requestFailed(480, "系统存储失败请重试");
        }
        return $this->response()->array($response);
    }
}