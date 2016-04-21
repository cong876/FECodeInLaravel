<?php

namespace App\Http\Controllers\Operator;


use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Repositories\DetailPositiveExtra\DetailPositiveExtraRepositoryInterface;
use App\Repositories\Requirement\RequirementRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Helper\LogTest;
use App\Helper\LeanCloud;
use App\Helper\WXNotice;
use App\Helper\Mail;
use App\Models\User;

class SplitOrderController extends Controller
{

    private $item, $detail_positive, $mainOrder, $suborder, $requirement;

    function __construct(ItemRepositoryInterface $item, DetailPositiveExtraRepositoryInterface $detail_positive,
                         MainOrderRepositoryInterface $mainOrder, SubOrderRepositoryInterface $subOrder,
                         RequirementRepositoryInterface $requirement)
    {

        $this->middleware('operator');

        $this->detail_positive = $detail_positive;

        $this->item = $item;

        $this->mainOrder = $mainOrder;

        $this->suborder = $subOrder;

        $this->requirement = $requirement;

    }

    /*
     *
     * 渲染待拆分订单页控制器
     *
     */

    function index(Request $request)
    {
        $main = $this->mainOrder->getById($request->mainOrderId);
        $operatorId = $main->requirement->operator_id;
        $operator_id = Auth::user()->employee->employee_id;
        $count_subOrders = count($main->subOrders);
        if ($operatorId != $operator_id && Auth::user()->employee->op_level <= 3) {
            abort(421);
        } else {
            if ($count_subOrders != 0) {
                for ($i = 0; $i < $count_subOrders; $i++) {
                    $items = $main->subOrders[$i]->items;
                    $price = 0;
                    foreach ($items as $item) {
                        $price += $item->skus->first()->sku_price * $item->skus->first()->sku_inventory;
                    }
                    $main->subOrders[$i]->sub_order_price = $price + $main->subOrders[$i]->postage;
                }
            }
            $this->mainOrder->getMainOrderPrice($main);
            return view('operation.splitOrder')->with(['mainOrder' => $main]);
        }
    }

    public function dealOrderDetail(Request $request)
    {
        $mainOrder = $this->mainOrder->getById($request->mainOrderId);
        return view('operation.orderDetail')->with(['mainOrder' => $mainOrder]);
    }

    /*
     *
     * 新增商品
     *
     */

    public function createItem(Request $request)
    {
        $main_id = $request->mainOrderId;
        $mainOrder = $this->mainOrder->getById($main_id);
        if (Auth::user()->employee->employee_id != $mainOrder->requirement->operator_id) {
            return response()->json(['status' => 414]);
        }
        $input_price = $request->price;
        $input_title = $request->title;
        $input_pics = $request->pic_urls;
        $input_des = $request->description;
        $input_number = $request->number;
        $memo = $request->opnote;

        if ($input_pics == null) {
            $input_pics = [];
        }
        $newItem = $this->item->create(array('title' => $input_title, 'pic_urls' => $input_pics, 'price' => $input_price, 'item_type' => 1, 'country_id' => 11),
            $mainOrder->requirement->operator_id, null, true, array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number, 'sku_price' => $input_price)),
            array('hlj_buyer_description' => $input_des, 'number' => $input_number,
                'hlj_admin_response_information' => $memo
            ));
        $item_id = $newItem->item_id;
        $mainOrder->items()->attach($item_id);
        $this->mainOrder->getMainOrderPrice($mainOrder);
        $response_item = $this->item->getById($item_id);
        $response = $response_item->item_id;
        LogTest::writeTestLog(Auth::user()->employee->real_name . '向逻辑主订单中添加了新商品', ['需求号' => $mainOrder->requirement->requirement_number, '主订单ID' => $main_id, '商品ID' => $item_id]);
        return response()->json($response);
    }

    /*
     *
     * 更新商品
     *
     */
    public function updateCreatedItem(Request $request)
    {
        $main_id = $request->mainOrderId;
        $mainOrder = $this->mainOrder->getById($main_id);
        if (Auth::user()->employee->employee_id != $mainOrder->requirement->operator_id) {
            return response()->json(['status' => 414]);
        }
        $input_price = $request->price;
        $input_title = $request->title;
        $input_pics = $request->pic_urls;
        $input_des = $request->description;
        $input_number = $request->number;
        $memo = $request->opnote;
        $item_id = $request->itemId;
        $item_update = $this->item->getById($item_id);
        if ($input_pics == null) {
            $input_pics = [];
        }
        $this->item->updateItem($item_update, array('title' => $input_title,
            'pic_urls' => $input_pics, 'price' => $input_price), null,
            array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number, 'sku_price' => $input_price)),
            array('hlj_buyer_description' => $input_des, 'number' => $input_number,
                'hlj_admin_response_information' => $memo
            ));

        LogTest::writeTestLog(Auth::user()->employee->real_name . '更新了商品' . $input_title, ['商品ID' => $item_id, '标题' => $input_title, '价格' => $input_price, '数量' => $input_number, '图片' => $input_pics, '运营备注' => $memo]);
        $this->mainOrder->getMainOrderPrice($mainOrder);
        $response_item = $this->item->getById($item_id);
        $response = $response_item->item_id;
        return response()->json($response);

    }

    /*
     *
     * 删除商品
     *
     */
    public function deleteCreatedItem(Request $request)
    {

        $main_id = $request->mainOrderId;
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $main = $this->mainOrder->getById($main_id);
        if (Auth::user()->employee->employee_id != $main->requirement->operator_id) {
            return response()->json(['status' => 414]);
        }
        $item->detail_positive->is_available = false;
        $item->detail_positive->save();
        if (($request->subOrderId) != "") {
            $sub_id = $request->subOrderId;
            $sub = $this->suborder->getById($sub_id);
            $subPrice = $sub->sub_order_price;
            if (!empty($sub->items->where('item_id', $item_id)->first())) {
                $price = $sub->items->where('item_id', $item_id)->first()->skus->first()->sku_price;
                $number = $sub->items->where('item_id', $item_id)->first()->skus->first()->sku_inventory;
                $newPrice = $subPrice - ($price * $number);
                $this->suborder->updateSubOrder($sub, array('sub_order_price' => $newPrice));
                $sub->items()->detach($item_id);
            }
        }
        $main->items()->detach($item_id);
        $this->item->deleteItem($item);
        LogTest::writeTestLog(Auth::user()->employee->real_name . '删除了生成的商品' . $item->title, ['商品ID' => $item_id]);
        $this->mainOrder->getMainOrderPrice($main);
        return response()->json(1);
    }

    /*
    *
    * 处理拆单合并订单等
    *
    */
    public function dealSubOrder(Request $request)
    {
        $main_id = $request->mainOrder_id;
        $mainOrder = $this->mainOrder->getById($main_id);
        if (Auth::user()->employee->employee_id != $mainOrder->requirement->operator_id) {
            return response()->json(['status' => 414]);
        }
        $sub_price = $request->subOrderPrice;
        $country_id = $request->country_id;
        if (count($request->item) != 0) {
            $item = $request->item;
            if (($request->to) == "") {
                $subOrder = $this->suborder->createSubOrder(array('main_order_id' => $main_id, 'buyer_id' => $mainOrder->hlj_id, 'postage' => $request->postage,
                    'sub_order_price' => $sub_price, 'country_id' => $country_id, 'seller_id' => $request->get('seller_id')));
                $subOrder->operator_id = $mainOrder->requirement->operator_id;
                $subOrder->save();
                LogTest::writeTestLog(Auth::user()->employee->real_name . '生成了子订单' . $subOrder->sub_order_number . ',并将其分配给' .
                    $subOrder->seller->country->name . '的' . $subOrder->seller->real_name, ['子订单号' => $subOrder->sub_order_number, '邮费' => $request->postage, '订单总价' => $sub_price]);
                foreach ($item as $v) {
                    if (empty($v['subOrder_number'])) {
                        $sub_id = $subOrder->sub_order_id;
                        $sub = $this->suborder->getById($sub_id);
                        $sub->items()->attach($v['item_id']);
                        $response = ['subOrder_id' => $sub_id];
                    } else {
                        $sub_id = $subOrder->sub_order_id;
                        $subOrder->items()->attach($v['item_id']);
                        $sub_origin_id = $v['subOrder_number'];
                        $sub_origin = $this->suborder->getById($sub_origin_id);
                        $sub_origin->items()->detach($v['item_id']);
                        $response = ['subOrder_id' => $sub_id];
                    }
                }

            } else {
                foreach ($item as $v) {
                    if (empty($v['subOrder_number'])) {
                        $sub_id = $request->to;
                        $sub = $this->suborder->getById($sub_id);
                        $this->suborder->updateSubOrder($sub, array('postage' => $request->postage, 'sub_order_price' => $sub_price,
                            'country_id' => $country_id, 'seller_id' => $request->seller_id));
                        $sub->items()->attach($v['item_id']);
                        $response = ['subOrder_id' => $sub_id];
                    } else {
                        $sub_id = $request->to;
                        $sub = $this->suborder->getById($sub_id);
                        $this->suborder->updateSubOrder($sub, array('postage' => $request->postage, 'sub_order_price' => $sub_price,
                            'country_id' => $country_id, 'seller_id' => $request->seller_id));
                        $sub->items()->attach($v['item_id']);
                        $sub_origin_id = $v['subOrder_number'];
                        $sub_origin = $this->suborder->getById($sub_origin_id);
                        $sub_origin->items()->detach($v['item_id']);
                        $response = ['subOrder_id' => $sub_id];
                    }
                    LogTest::writeTestLog(Auth::user()->employee->real_name . '更新了子订单' . $sub->sub_order_number . ',并将其分配给' .
                        $sub->seller->country->name . '的' . $sub->seller->real_name, ['子订单号' => $sub->sub_order_number, '邮费' => $request->postage, '订单总价' => $sub_price]);
                }
            }
        } else {
            $sub_id = $request->to;
            $sub = $this->suborder->getById($sub_id);
            $this->suborder->updateSubOrder($sub, array('postage' => $request->postage, 'sub_order_price' => $sub_price,
                'country_id' => $country_id, 'seller_id' => $request->seller_id));
            $response = ['subOrder_id' => $sub_id];


        }
        $mainOrder = $this->mainOrder->getById($main_id);
        $this->mainOrder->getMainOrderPrice($mainOrder);
        return response()->json($response);
    }

    /*
     *
     * 删除子订单
     *
     */
    public function deleteSubOrder(Request $request)
    {
        $sub_id = $request->subOrderId;
        $subOrder = $this->suborder->getById($sub_id);
        $main_id = $subOrder->main_order_id;
        $mainOrder = $this->mainOrder->getById($main_id);
        if (Auth::user()->employee->employee_id != $mainOrder->requirement->operator_id) {
            return response()->json(['status' => 414]);
        }
        $count = count($subOrder->items);
        for ($i = 0; $i < $count; $i++) {
//            $subOrder->items()->detach($subOrder->items[$i]->item_id);
//            $mainOrder->items()->detach($subOrder->items[$i]->item_id);
            $item_delete = $this->item->getById($subOrder->items[$i]->item_id);
            $this->item->deleteItem($item_delete);
        }
        $this->suborder->deleteSubOrder($subOrder);
        LogTest::writeTestLog(Auth::user()->employee->real_name . '删除了子订单' . $subOrder->sub_order_number, ['子订单号' => $subOrder->sub_order_number]);
        $this->mainOrder->getMainOrderPrice($mainOrder);
        return response()->json(1);
    }

    /*
     *
     * 删除逻辑主订单
     *
     */
    public function deleteMainOrder(Request $request)
    {
        $main_id = $request->mainOrderId;
        $mainOrder = $this->mainOrder->getById($main_id);
        if (Auth::user()->employee->employee_id != $mainOrder->requirement->operator_id) {
            return response()->json(['status' => 414]);
        }
        $requirement = $mainOrder->requirement;
        $requirement->state = 431;
        $hlj_id = $requirement->hlj_id;
        $buyer_openid = User::find($hlj_id)->openid;
        $details = $requirement->requirementDetails;
        $title = '';
        foreach ($details as $detail) {
            $detail->is_available = false;
            $title .= $detail->title . '；';
            $detail->save();
        }
        $requirement->save();
        $title = rtrim($title, '；');
        if (mb_strlen($title) > 12) {
            $title_send = mb_substr($title, 0, 12) . '...';
        } else {
            $title_send = $title;
        }
        $count = count($mainOrder->subOrders);
        for ($i = 0; $i < $count; $i++) {
            $sub_id = $mainOrder->subOrders[$i]->sub_order_id;
            $subOrder = $this->suborder->getById($sub_id);
            $count_item = count($subOrder->items);
//            for ($j = 0; $j < $count_item; $j++) {
//                $subOrder->items()->detach($subOrder->items[$j]->item_id);
//            }
            $this->suborder->deleteSubOrder($subOrder);
            LogTest::writeTestLog(Auth::user()->employee->real_name . '删除了生成的子订单', ['需求号' => $requirement->requirement_number, '子订单号' => $sub_id]);
        }
        $count_mainOrder = count($mainOrder->items);
        for ($i = 0; $i < $count_mainOrder; $i++) {
//            $mainOrder->items()->detach($mainOrder->items[$i]->item_id);
            $item_delete = $this->item->getById($mainOrder->items[$i]->item_id);
            $this->item->deleteItem($item_delete);
        }
        $this->mainOrder->deleteMainOrder($mainOrder);
        $notice = new WXNotice();
        $notice->requestCanceled($buyer_openid, $requirement->requirement_number, $title_send, '未报价');
        LogTest::writeTestLog(Auth::user()->employee->real_name . '删除了生成的主订单', ['需求号' => $requirement->requirement_number, '主订单号' => $main_id]);
        return response()->json(url('operator/waitResponse'));
    }

    /*
     *
     * 发送报价给买家和买手
     *
     */
    public function sendPrice(Request $request)
    {
        $main_id = $request->mainOrderId;
        $mainOrder = $this->mainOrder->getById($main_id);
        if ($mainOrder->main_order_state == 101) {
            $mainOrder->main_order_state = 301;
            $mainOrder->requirement->state = 301;
            $hlj_id = $mainOrder->requirement->hlj_id;
            $mobile = User::find($hlj_id)->mobile;
            $buyer_openid = User::find($hlj_id)->openid;
            $buyer = Buyer::where('hlj_id', $hlj_id)->first();
            $email = $mainOrder->requirement->user->email;
            $mainOrder->save();
            $mainOrder->requirement->save();
            $subOrders = $mainOrder->subOrders;
            $count = count($subOrders);
            $user = Auth::user();
            $items = $mainOrder->items;
            $title_main = '';
            $title_mail = '';
            if (count($items) > 1) {
                $title_mail = $items[0]->title . '...';
            } else {
                $title_mail = $items[0]->title;
            }
            foreach ($items as $item) {
                $title_main .= $item->title . '；';
            }
            $title_main = rtrim($title_main, '；');
            if (mb_strlen($title_main) > 8) {
                $title_send = mb_substr($title_main, 0, 8) . '...';
            } else {
                $title_send = $title_main . '。';
            }
            $notice = new WXNotice();
            for ($i = 0; $i < $count; $i++) {
                $suborder = $subOrders[$i];
                if ($suborder->is_available == 1) {
                    $suborder->sub_order_state = 201;
                    $suborder->created_offer_time = date('Y-m-d H:i:s');
                    $this->suborder->createOrUpdateSubOrderBidSnapshot($suborder);
                    $suborder->operator_id = $mainOrder->requirement->operator_id;
                    $sub_items = $suborder->items;
                    $title_sub = '';
                    foreach ($sub_items as $item) {
                        $title_sub .= $item->title . '；';
                    }
                    $title_sub = rtrim($title_sub, '；');
                    if (mb_strlen($title_sub) > 12) {
                        $title_notice = mb_substr($title_sub, 0, 12) . '...';
                    } else {
                        $title_notice = $title_sub;
                    }
                    $price = $suborder->sub_order_price;
                    $seller = $suborder->seller;
                    $seller->seller_gmv += $price;
                    $seller->seller_receive_orders_num += 1;
                    $seller_openid = $seller->user->openid;
                    $seller->save();
                    $buyer->buyer_gmv += $price;
                    $buyer->save();
                    $number = $suborder->sub_order_number;
                    $time = $suborder->created_at;
                    $suborder->save();
                    $notice->tellBuyerPay($buyer_openid, $number,
                        $title_notice, sprintf('%.2f', strval($price)), $time, $suborder->sub_order_id);
                    LogTest::writeTestLog(Auth::user()->employee->real_name . '发送了子订单' . $mainOrder->subOrders[$i]->sub_order_number . '的报价给买手' . $mainOrder->subOrders[$i]->seller->real_name, ['子订单号' => $mainOrder->subOrders[$i]->sub_order_number, '总价' => $price]);

                }
            }
            if ($mainOrder->requirement->is_activity == true) {
                $subOrder = $mainOrder->subOrders->first();
                $id = $mainOrder->requirement->activities->first()->activity_id;
                $subOrder->activities()->attach($id);
            }
            Mail::MailToBuyerForPay(array($email), [$title_mail]);
            LeanCloud::sendBuyerPayNotification($mobile, $title_send, $user->mobile);
            return redirect(url('operator/waitSplit'));
        } else {
            abort(413);
        }
    }
}
