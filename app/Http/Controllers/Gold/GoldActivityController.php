<?php

namespace App\Http\Controllers\Gold;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Repositories\DetailPassiveExtra\DetailPassiveExtraRepositoryInterface;
use App\Repositories\GroupItem\GroupItemRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\TaskItem;
use App\Helper\CreateGoldStatement;
use App\Helper\IncreaseGold;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class GoldActivityController extends Controller
{
    protected $item, $detail_passive, $mainOrder, $suborder, $groupItem;

    function __construct(ItemRepositoryInterface $item, DetailPassiveExtraRepositoryInterface $detail_passive,
                         MainOrderRepositoryInterface $mainOrder, SubOrderRepositoryInterface $subOrder,
                         GroupItemRepositoryInterface $groupItem)
    {

        $this->middleware('wechatauth');
        $this->middleware('createGold', ['only' => ['getGoldActivity']]);
        $this->middleware('forward', ['only' => ['getGoldActivity']]);
        $this->middleware('goldCache', ['only' => ['getGoldActivity']]);
        $this->middleware('subscribe', ['only' => ['getGoldActivity']]);

        $this->detail_passive = $detail_passive;

        $this->item = $item;

        $this->mainOrder = $mainOrder;

        $this->suborder = $subOrder;

        $this->groupItem = $groupItem;

    }

    /**
     * 买家发起金币兑换请求生成订单
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createGoldOrder(Request $request)
    {
        $user = Auth::user();
        $number = $request->number;
        if ($request->gold*$number > $user->golds->current_gold_num) {
            abort(418);
        }
        $hlj_id = $user->hlj_id;
        $item_id = $request->item_id;
        $item = $this->item->getById($item_id);
        $receivingAddress_id = $request->receiving_address_id;
        $sku = $item->skus->first();
        if ($sku->sku_inventory < $number) {
            abort(417);
        }
        $sku->sku_inventory -= $number;
        $sku->save();
        $mainOrder = $this->mainOrder->createMainOrder(array('main_order_state' => 301, 'main_order_price' => 0), $hlj_id);
        $mainOrder->items()->attach($item_id);
        $meta = json_decode($item->attributes);
        $order_info = $meta->activity_meta;
        $seller_id = $order_info->seller_id;
        $operator_id = $order_info->operator_id;
        $suborder = $this->suborder->createSubOrder(array('main_order_id' => $mainOrder->main_order_id, 'buyer_id' => $hlj_id, 'postage' => 0,
            'sub_order_price' => 0, 'country_id' => $item->country_id, 'seller_id' => $seller_id, 'operator_id' => $operator_id,
            'receiving_addresses_id' => $receivingAddress_id, 'order_type' => 2));
        $suborder->sub_order_state = 501;
        $suborder->created_offer_time = date('Y-m-d H:i:s');
        $suborder->payment_time = date('Y-m-d H:i:s');
        $suborder->save();
        $this->groupItem->createGroupItem(array('item_id' => $item_id, 'sub_order_id' => $suborder->sub_order_id,
            'number' => $number, 'memo' => '', 'hlj_id' => $hlj_id));
        $suborder->items()->attach($item_id);
        $statement = new CreateGoldStatement($user);
        $task = TaskItem::where('item_id', $item_id)->first();
        $statement->decrease($task, $user, $number);
        $this->suborder->createOrUpdateSubOrderPaidSnapshot($suborder);
        return redirect(url('/user/MyOrder#toNeedSend'));
    }

    /*
     *
     * 进入转发送金币活动页面
     *
     */
    public function getGoldActivity(Request $request)
    {
        $items = DB::table('items')->join('task_items', function ($join) {
            $join->on('items.item_id', '=',
                'task_items.item_id');
        })->where('item_type', 3)->where('is_on_shelf', true)->where('is_available',1)->orderBy('coins')->get();
        $items_trans = [];
        foreach ($items as $item) {
            $id = $item->item_id;
            $title = $item->title;
            $description = Item::find($id)->detail_passive->description;
            $meta = json_decode(Item::find($id)->attributes);
            $order_info = $meta->activity_meta;
            $market_price = $order_info->market_price;
            $pic_url = $item->pic_urls;
            $coins = $item->coins;
            $item_info = ['id' => $id, 'title' => $title, 'description' => $description, 'pic_url' => $pic_url, 'coins' => $coins,
                'marketPrice' => $market_price, 'limitedCount' => Item::find($id)->skus->first()->sku_inventory];
            $item_json = json_encode($item_info);
            array_push($items_trans, $item_json);
        }
        $user = Auth::user();
        $user_headImg = $user->headimgurl;
        $user_coins = isset($user->golds) ? $user->golds->current_gold_num : 0;
        $user_name = $user->nickname;
        $user_info = ['headImgUrl' => $user_headImg, 'nickname' => $user_name, 'coins' => $user_coins];
        $supporters = $user->supporters;
        if ($supporters) {
            $supp = $supporters->forPage(1, 10);
            $supporters_page = new LengthAwarePaginator($supp, count($supporters), 10, null, array('path' => Paginator::resolveCurrentPath()));
            $supporter_trans = [];
            foreach ($supporters_page as $supporter) {
                $this_supporter = $supporter->support_user;
                $supporter_headImg = $this_supporter->headimgurl;
                $supporter_nickname = $this_supporter->nickname;
                $supporter_info = ['support_headImgUrl' => $supporter_headImg, 'support_nickname' => $supporter_nickname];
                array_push($supporter_trans, $supporter_info);
            }
        } else {
            $supporter_trans = "";
        }
        $regionInstance = \App\Helper\ChinaRegionsHelper::getInstance();
        if ($receivingAddresses = $user->receivingAddresses) {
            $address_trans = [];
            foreach ($receivingAddresses as $receivingAddress) {
                if ($receivingAddress->is_available == 1) {
                    $province_code = $receivingAddress->first_class_area;
                    $city_code = $receivingAddress->second_class_area;
                    $county_code = $receivingAddress->third_class_area;
                    $street_address = $receivingAddress->street_address;
                    $province_level = $regionInstance->getRegionByCode($province_code)->name;
                    if ($city_code == 1) {
                        $city_level = "";
                    } else {
                        $city_level = $regionInstance->getRegionByCode($city_code)->name;
                    }
                    if ($county_code == 1) {
                        $county_level = "";
                    } else {
                        $county_level = $regionInstance->getRegionByCode($county_code)->name;
                    }
                    $receiver_name = $receivingAddress->receiver_name;
                    $receiver_mobile = $receivingAddress->receiver_mobile;
                    $receiver_zip_code = $receivingAddress->receiver_zip_code;
                    $address_id = $receivingAddress->receiving_addresses_id;
                    $is_default = $receivingAddress->is_default;
                    $address_info = ['receiver_name' => $receiver_name, 'receiver_mobile' => $receiver_mobile, 'address_id' => $address_id,
                        'receiver_address' => $province_level . $city_level . $county_level . $street_address . '，' . $receiver_zip_code,
                        'isDefault' => $is_default];
                    array_push($address_trans, $address_info);
                }
            }
        } else {
            $address_trans = "";
        }
        if ($user->mobile) {
            $register_state = 'true';
        } else {
            $register_state = 'false';
        }
        $data_json = json_encode(['user' => $user_info, 'items' => $items_trans, 'supporters' => $supporter_trans,
            'address_info' => $address_trans, 'register_state' => $register_state]);
        if ($master = Cache::get($user->openid . '_forward_infos')) {
            $keys = array_keys($master);
            foreach ($keys as $key) {
                if ($key == $request->sender) {
                    $add_info = new IncreaseGold();
                    $add_info->addGold($master[$key][0], $master[$key][1], $master[$key][2]);
                    unset($master[$key]);
                }
            }
            if (count($master) > 0) {
                Cache::forever($user->openid . '_forward_infos', $master);
            } else {
                Cache::forget($user->openid . '_forward_infos');
            }
        }

        return view('activities.goldActivity')->with(['activityData' => $data_json]);
    }

    /*
     *
     * 谁打赏过我查看更多按钮请求
     *
     */
    public function getMoreSupporters(Request $request)
    {
        $user = Auth::user();
        $supporters = $user->supporters;
        if ($supporters) {
            $page = $request->page;
            $supp = $supporters->forPage($page, 10);
            $supporters_page = new LengthAwarePaginator($supp, count($supporters), 10, null, array('path' => Paginator::resolveCurrentPath()));
            $supporter_trans = [];
            foreach ($supporters_page as $supporter) {
                $this_supporter = $supporter->support_user;
                $supporter_headImg = $this_supporter->headimgurl;
                $supporter_nickname = $this_supporter->nickname;
                $supporter_info = ['support_headImgUrl' => $supporter_headImg, 'support_nickname' => $supporter_nickname];
                array_push($supporter_trans, $supporter_info);
            }
        } else {
            $supporter_trans = "";
        }
        $data_json = ['supporters' => $supporter_trans];
        return response()->json($data_json);
    }


}