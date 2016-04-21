<?php

namespace App\Http\Controllers\Payment;

use App\Helper\LeanCloud;
use App\Models\GroupItem;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Pingpp\Charge;
use Pingpp\Pingpp;
use App\Models\SubOrder;
use App\Helper\LogTest;
use App\Helper\WXNotice;
use Illuminate\Support\Facades\Auth;
use App\Helper\SuborderNumberGeneratorHelper;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use Pingpp\Error\Base;

class PaymentController extends Controller
{
    const SUBORDER_EXPIRE_TIME = 259200;
    const SUBORDER_IS_NOT_AFFORDABLE = "Wrong Suborder Type";
    const SUBORDER_IS_EXPIRED = "Suborder Expired";
    const BUYER_OVER_BUY = "Current Overbuy Group Items";
    const UNSUPPORTED_CHANNEL = "Current Channel Is Not Supported";

    private $PingXXApiKey;
    private $suborder;

    function __construct(SubOrderRepositoryInterface $suborder)
    {
        $this->PingXXApiKey = config('pingpp.apiKey');
        $this->middleware('wechatauth');
        $this->suborder = $suborder;
    }

    /**
     *
     * 提交支付商品信息，返回Charge对象供前端调用以拉起微信支付
     * @param Request $request
     * @return Charge
     */
    public function postPayInformation(Request $request)
    {
        // 只有241, 201状态订单可以支付
        $subOrder = SubOrder::where('sub_order_number', $request['order_no'])->first();
        if ($user_id = $request['user_id']) {
            $currentOpenId = User::find($user_id)->openid;
        } else {
            $currentOpenId = Auth::user()->openid;
        }

        if ($subOrder->sub_order_state != 201 && $subOrder->sub_order_state != 241) {
            dd(self::SUBORDER_IS_NOT_AFFORDABLE);
        }

        // 渠道附加参数
        $extra = null;
        switch ($request['channel']) {
            case 'wx_pub':
                $extra = array('open_id' => $currentOpenId);
                break;
            default :
                dd(self::UNSUPPORTED_CHANNEL);
        }

        // 再次计算订单价格,优先使用拍下快照价格,若无快照使用付款时实时价格
        $priceArray = [];
        if ($snapshot = $subOrder->snapshot) {
            if ($bid_snapshot = $snapshot->bid_snapshot) {
                $snap = json_decode($bid_snapshot);
                $items = $snap->items;
                foreach ($items as $item) {
                    $item_count = $item->number;
                    array_push($priceArray, $item->price * $item_count);
                }

                $priceReCalculated = collect($priceArray)->sum() + $subOrder->postage;
            }
        } else {
            $item_collection = $subOrder->items->map(function ($item) use ($subOrder) {
                $item_count = $item->item_type == 1 ?
                    $item->detail_positive->number :
                    GroupItem::where('sub_order_id', $subOrder->sub_order_id)->first()->number;
                return [
                    'id' => $item->item_id,
                    'title' => $item->title,
                    'pic_urls' => $item->pic_urls,
                    'item_type' => $item->item_type,
                    'number' => $item_count,
                    'price' => $item->price,
                    'total_price' => $item->price * $item_count
                ];
            });
            $priceReCalculated = $item_collection->sum('total_price') + $subOrder->postage;
        }

        $subject = $request['title'];
        if (mb_strlen($subject) > 26) {
            $subject = mb_substr($request['title'], 0, 24) . '...';
        }

        // 附着收件地址人地址信息
        $subOrder->update(array(
            "receiving_addresses_id" => $request['receiving_address_id'],
        ));


        $this->suborder->createOrUpdateSubOrderPaidSnapshot($subOrder);

        // 通过所有检测后,发起支付请求
        Pingpp::setApiKey($this->PingXXApiKey);

        try {
            $charge =  Charge::create(
                array(
                    'order_no' => $subOrder->sub_order_number,
                    'app' => array('id' => 'app_WzDmrT0CC8G0HSen'),
                    'channel' => $request['channel'],
                    'amount' => $priceReCalculated * 100,
                    'client_ip' => $request->ip(),
                    'currency' => 'cny',
                    'subject' => $subject,
                    'body' => 'Default Null',
                    'extra' => $extra,
                )
            );
            return $charge;
        } catch (Base $e) {
            $subOrder->update(array(
                "sub_order_number" => SuborderNumberGeneratorHelper::generateSuborderNumber()
            ));
            $charge =  Charge::create(
                array(
                    'order_no' => $subOrder->sub_order_number,
                    'app' => array('id' => 'app_WzDmrT0CC8G0HSen'),
                    'channel' => $request['channel'],
                    'amount' => $request['amount'],
                    'client_ip' => $request->ip(),
                    'currency' => 'cny',
                    'subject' => $subject,
                    'body' => 'Default Null',
                    'extra' => $extra,
                )
            );
            return $charge;
        }
    }

    // 提交退款
    public function postRefundInformation(Request $request)
    {
        if ($request['refundAmount'] <= 0) {
            abort(491);
        }

        $subOrder = SubOrder::where('sub_order_id', $request->sub_order_id)->first();
        $description = $request['refundDescription'];

        // 商品的部分退款
        if (!empty($request['refundItemNumber'])) {
            $description .= 'YeYeTech' . $request['refundItemNumber'] . 'YeYeTech' . $request['refundItemTitle'] . 'YeYeTech' . $request['refundItemId'];
        }
        // 发起退款请求，获得退款对象
        Pingpp::setApiKey($this->PingXXApiKey);
        $charge = Charge::retrieve($subOrder['charge_id']);
        $result = $charge->refunds->create(
            array(
                'amount' => $request['refundAmount'] * 100,
                'description' => $description
            )
        );
        $notice = new WXNotice();
        // 部分退款
        if (!empty($request['refundItemId'])) // 写入退款表
        {
            SubOrder::where('charge_id', $subOrder['charge_id'])->first()->refunds()->create(
                array(
                    'item_id' => $request['refundItemId'],
                    'refund_inventory_count' => $request['refundItemNumber'],
                    'refund_price' => $result['amount'] / 100.00,
                    'ppp_status' => $result['status'],
                    'ppp_refund_order_id' => $result['id'],
                    'charge_id' => $result['charge'],
                    'is_successful' => $result['succeed'],
                    'description' => $request['refundItemTitle'] . '###' . explode('YeYeTech', $result['description'])[0]
                )
            );
            if ($result['status'] == 'pending') {
                // 推送
                $refund_price = sprintf('%.2f', $result['amount'] / 100.00);
                $subOrder->refund_price += $request['refundAmount'];
                $subOrder->save();
                $pos = strpos($description, "###");
                $des = substr($description, $pos + 3);
                $number = $subOrder->sub_order_number;
                LogTest::writeTestLog(Auth::user()->employee->real_name .
                    '退款了' . $request['refundItemNumber'] .
                    '件商品', ['子订单号' => $number, '商品ID' => $request['refundItemId'],
                    '退款金额' => $refund_price, '退款原因' => $des]);
                $seller_openid = $subOrder->seller->user->openid;
                $number = $subOrder->sub_order_number;
                $sub_number = substr($number, -4, 4);
                $refund_price_notice = sprintf('%.2f', $result['amount'] / 100.00);
                $notice->notifySellerRefund($seller_openid, $sub_number, $refund_price_notice);
            }
        } else {
            SubOrder::where('charge_id', $subOrder['charge_id'])->first()->refunds()->create(
                array(
                    'refund_type' => 2,
                    'refund_price' => $result['amount'] / 100.00,
                    'ppp_status' => $result['status'],
                    'ppp_refund_order_id' => $result['id'],
                    'charge_id' => $result['charge'],
                    'is_successful' => $result['succeed'],
                    'description' => $result['description']
                )
            );
            if ($result['status'] == 'pending') {
                $refund_price = floatval($result['amount'] / 100.00);
                $subOrder->update(array(
                    "sub_order_state" => 301,
                    "refund_price" => $subOrder->refund_price + $request['refundAmount']
                ));
                LogTest::writeTestLog(Auth::user()->employee->real_name . '为订单' .
                    $subOrder->sub_order_number . '退款', ['子订单号' => $subOrder->sub_order_number,
                    '退款金额' => $refund_price, '退款原因' => $description]);
                $seller_openid = $subOrder->seller->user->openid;
                $number = $subOrder->sub_order_number;
                $sub_number = substr($number, -4, 4);
                $refund_price_notice = sprintf('%.2f', $result['amount'] / 100.00);
                $notice->notifySellerRefund($seller_openid, $sub_number, $refund_price_notice);
            }

        }

        // 退款推送
        $snapshot = json_decode($subOrder->snapshot->paid_snapshot);
        $title = $snapshot->title;

        $title = rtrim($title, ';');
        if (count($title) > 12) {
            $title = mb_substr($title, 0, 12) . '...';
        }

        $hlj_id = $subOrder->buyer_id;
        $mobile = User::find($hlj_id)->mobile;
        $buyer_openid = User::find($hlj_id)->openid;
        $notice = new WXNotice();
        $support_phone = $subOrder->operator->user->mobile;

        LeanCloud::refundBuyer($mobile, sprintf('%.2f', $result['amount'] / 100.00),
            $subOrder->sub_order_number, $support_phone);
        $notice->refundNotify($buyer_openid, sprintf('%.2f', $result['amount'] / 100.00),
            $title, $subOrder->sub_order_number, $request['refundDescription']);
        return back();
    }

    public function paySucceed()
    {
        return view('paySuccess');
    }
}
