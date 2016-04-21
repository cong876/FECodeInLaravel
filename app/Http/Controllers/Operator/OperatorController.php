<?php

namespace App\Http\Controllers\Operator;

use App\Events\MailToSellerForDeliverEvent;
use App\Helper\WXNotice;
use App\Models\Buyer;
use App\Models\Employee;
use App\Models\GroupItem;
use App\Models\ItemTag;
use App\Models\PaymentMethod;
use App\Models\Requirement;
use App\Models\RequirementDetail;
use App\Models\Seller;
use App\Models\SubOrder;
use App\Models\SubOrderMemo;
use App\Models\User;
use App\Models\SellerPrison;
use App\Models\TransferReason;
use App\Models\RequirementMemo;
use App\Models\SellerMemo;
use App\Models\BuyerMemo;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Requirement\RequirementRepositoryInterface;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Repositories\DetailPositiveExtra\DetailPositiveExtraRepositoryInterface;
use App\Repositories\Seller\SellerRepositoryInterface;
use App\Repositories\DeliveryInfo\DeliveryInfoRepositoryInterface;
use App\Repositories\Buyer\BuyerRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helper\LogTest;
use App\Helper\LeanCloud;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Helper\ChinaRegionsHelper;
use Maatwebsite\Excel\Facades\Excel;
use Wilddog\WilddogLib;


class OperatorController extends Controller
{
    private $requirement, $item, $detail_positive,
        $mainOrder, $suborder, $seller,
        $deliveryInfo, $buyer, $regionInstance;

    function __construct(RequirementRepositoryInterface $requirement, ItemRepositoryInterface $item,
                         DetailPositiveExtraRepositoryInterface $detail_positive, MainOrderRepositoryInterface $mainOrder,
                         SubOrderRepositoryInterface $subOrder, SellerRepositoryInterface $seller,
                         DeliveryInfoRepositoryInterface $deliveryInfo, BuyerRepositoryInterface $buyer)
    {
        $this->requirement = $requirement;

        $this->middleware('operator');

        $this->detail_positive = $detail_positive;

        $this->item = $item;

        $this->mainOrder = $mainOrder;

        $this->suborder = $subOrder;

        $this->seller = $seller;

        $this->deliveryInfo = $deliveryInfo;

        $this->buyer = $buyer;

        $this->regionInstance = ChinaRegionsHelper::getInstance();

    }

    /**
     *
     * 需求待领取页
     * @return $this
     */
    public function waitAccept()
    {
        $requirements = $this->requirement->getAllWaitDispatchRequirementsWithPaginate(15);
        return view('operation.requirementWaitAccept')->with(['requirements' => $requirements]);
    }

    /**
     *
     * 展示所有该运营接收的需求
     * @return $this
     */
    public function waitResponse()
    {
        if (Auth::user()->employee->op_level > 3) {
            $requirements = $this->requirement->getAllWaitResponseRequirementsWithPaginate(15);
        } elseif (Auth::user()->employee->op_level == 3) {
            $requirements = $this->requirement->getAllWaitDispatchRequirementsWithPaginateByEmployeeId(15, Auth::user()->employee->employee_id);
        }

        return view('operation.requirementWaitResponse')->with(['requirements' => $requirements]);
    }

    public function waitRes()
    {
        return view('operation.ResWaitResponse');
    }

    public function getRequirement()
    {
        return Requirement::with('requirementDetails', 'country', 'user')->orderBy('created_at', 'desc')->paginate(4);
    }

    /**
     *
     * 展示所有待拆单需求
     * @return $this
     */
    public function waitSplit()
    {
        if (Auth::user()->employee->op_level > 3) {
            $requirements = $this->requirement->getAllWaitSplitRequirementsWithPaginate(15);
        } elseif (Auth::user()->employee->op_level == 3) {
            $requirements = $this->requirement->getAllWaitSplitRequirementsWithPaginateByEmployeeId(15, Auth::user()->employee->employee_id);
        }
        return view('operation.requirementWaitSplit')->with(['requirements' => $requirements]);
    }

    /**
     *
     * 展示所有待发送报价订单
     * @return $this
     */
    public function waitSendPrice()
    {
        $mainOrders = $this->mainOrder->getAllWaitSendPriceOrdersWithPaginate(15);
        return view('operation.requirementWaitSendPrice')->with(['mainOrders' => $mainOrders]);
    }

    /**
     *
     * 展示所有已完成需求
     * @return $this
     */
    public function showFinishedRequirement()
    {
        $requirements = $this->requirement->getAllFinishedRequirementsWithPaginate(15);
        return view('operation.requirementFinished')->with(['requirements' => $requirements]);
    }

    /**
     *
     * 展示所有已关闭需求
     * @return $this
     */
    public function showClosedRequirement()
    {
        $requirements = $this->requirement->getAllClosedRequirementsWithPaginate(15);
        return view('operation.requirementClosed')->with(['requirements' => $requirements]);
    }

    /**
     *
     * 待付款订单详情
     * @param Request $request
     * @return $this
     */
    public function editOffer(Request $request)
    {
        $operator_id = Auth::user()->employee->employee_id;
        $id = $request->subOrderId;
        $subOrder = $this->suborder->getbyId($id);
        if (($operator_id == $subOrder->operator_id) || (Auth::user()->employee->op_level > 3)) {
            return view('operation.orderDetail')->with(['suborder' => $subOrder]);
        } else {
            abort(411);
        }
    }

    /**
     *
     * 渲染待付款页
     * @param Request $request
     * @return $this
     */
    public function waitPay(Request $request)
    {
        $sub_orders = SubOrder::where('sub_order_state', 201)->get()->sortByDesc('updated_at');
        $subs = $sub_orders->filter(function ($sub) {
            if ($sub->operator_id == Auth::user()->employee->employee_id) {
                return $sub;
            }
        });
        $page = $request->page;
        $suborder = $subs->forPage($page, 15);
        $operator = Auth::user()->employee;
        if ($operator->op_level > 3) {
            $suborders = $this->suborder->getAllWaitPayOrderWithPaginate(15);
        } elseif ($operator->op_level == 3) {
            $suborders = new LengthAwarePaginator($suborder, count($subs), 15, null, array('path' => Paginator::resolveCurrentPath()));
        }
        return view('operation.orderWaitPay')->with(['suborders' => $suborders]);
    }

    /**
     *
     * 渲染待发货页
     * @param Request $request
     * @return $this
     */
    public function waitDelivery(Request $request)
    {
        $subs = SubOrder::where('sub_order_state', 501)->get()->sortByDesc('updated_at');
        $sub_orders = $subs->filter(function ($sub_order) {
            if ($sub_order->operator_id == Auth::user()->employee->employee_id) {
                return $sub_order;
            }
        });
        $page = $request->page;
        $suborder = $sub_orders->forPage($page, 15);
        $operator = Auth::user()->employee;
        if ($operator->op_level > 3) {
            $suborders = $this->suborder->getAllWaitDeliveryOrderWithPaginate(15);
        } elseif ($operator->op_level == 3) {
            $suborders = new LengthAwarePaginator($suborder, count($sub_orders), 15, null, array('path' => Paginator::resolveCurrentPath()));
        }
        return view('operation.orderWaitDelivery')->with(['suborders' => $suborders]);

    }

    public function waitDeliveryGTSevenDays(Request $request)
    {
        $sevenDaysSecond = date('Y-m-d H:i:s', time() - 7 * 24 * 3600);

        $subs = SubOrder::where('sub_order_state', 501)->where('payment_time', '<=', $sevenDaysSecond)
            ->get()->sortByDesc('updated_at');
        $sub_orders = $subs->filter(function ($sub_order) {
            if ($sub_order->operator_id == Auth::user()->employee->employee_id) {
                return $sub_order;
            }
        });
        $page = $request->page;
        $suborder = $sub_orders->forPage($page, 15);
        $operator = Auth::user()->employee;
        if ($operator->op_level > 3) {
            $suborder = $subs->forPage($page, 15);
            $suborders = new LengthAwarePaginator($suborder, count($subs), 15, null, array('path' => Paginator::resolveCurrentPath()));
        } elseif ($operator->op_level == 3) {
            $suborders = new LengthAwarePaginator($suborder, count($sub_orders), 15, null, array('path' => Paginator::resolveCurrentPath()));
        }
        return view('operation.orderWaitDelivery7days')->with(['suborders' => $suborders]);

    }

    /**
     *
     * 待发货详情页
     * @param Request $request
     * @return $this
     */
    public function editDeliveryOrder(Request $request)
    {
        $operator_id = Auth::user()->employee->employee_id;
        $id = $request->subOrderId;
        $subOrder = $this->suborder->getbyId($id);
        if (($operator_id == $subOrder->operator_id) || (Auth::user()->employee->op_level > 3)) {
            return view('operation.orderDeliveryDetail')->with(['suborder' => $subOrder]);
        } else {
            abort(421);
        }
    }

    /*
     *
     * 待审核详情页
     *
     */
    public function editAuditingOrder(Request $request)
    {
        $operator_id = Auth::user()->employee->employee_id;
        $id = $request->subOrderId;
        $subOrder = $this->suborder->getbyId($id);
        if (($operator_id == $subOrder->operator_id) || (Auth::user()->employee->op_level > 3)) {
            return view('operation.orderAuditingDetail')->with(['suborder' => $subOrder]);
        } else {
            abort(421);
        }
    }

    /*
     *
     * 订单管理已发货详情页
     *
     */
    public function editHasDeliveredOrder(Request $request)
    {
        $operator_id = Auth::user()->employee->employee_id;
        $id = $request->subOrderId;
        $subOrder = $this->suborder->getbyId($id);
        if (($operator_id == $subOrder->operator_id) || (Auth::user()->employee->op_level > 3)) {
            return view('operation.orderHasDeliveredDetail')->with(['suborder' => $subOrder]);
        } else {
            abort(421);
        }
    }

    /*
     *
     * 订单管理已完成详情页
     *
     */
    public function editHasFinishedOrder(Request $request)
    {
        $operator_id = Auth::user()->employee->employee_id;
        $id = $request->subOrderId;
        $subOrder = $this->suborder->getbyId($id);
        if (($operator_id == $subOrder->operator_id) || (Auth::user()->employee->op_level > 3)) {
            return view('operation.orderHasFinishedDetail')->with(['suborder' => $subOrder]);
        } else {
            abort(421);
        }
    }

    /*
     *
     * 订单管理拒单待分配买手详情页
     *
     */
    public function editSellerAssignOrder(Request $request)
    {
        $operator_id = Auth::user()->employee->employee_id;
        $id = $request->subOrderId;
        $subOrder = $this->suborder->getbyId($id);
        if (($operator_id == $subOrder->operator_id) || (Auth::user()->employee->op_level > 3)) {
            return view('operation.orderSellerAssignDetail')->with(['suborder' => $subOrder]);
        } else {
            abort(421);
        }
    }

    /*
     *
     * 渲染已发货页
     *
     */
    public function hasDelivered(Request $request)
    {
        $subs = SubOrder::where('sub_order_state', 601)->get()->sortByDesc('updated_at');
        $sub_orders = $subs->filter(function ($sub_order) {
            if (($sub_order->operator_id == Auth::user()->employee->employee_id) &&
                (!Cache::get('suborder:' . $sub_order->sub_order_id . ':secondaryDeliver'))
            ) {
                return $sub_order;
            }
        });
        $page = $request->page;
        $suborder = $sub_orders->forPage($page, 15);
        $operator = Auth::user()->employee;
        if ($operator->op_level > 3) {
            $suborders = $this->suborder->getAllHasDeliveredOrderWithPaginate(15);
        } elseif ($operator->op_level == 3) {
            $suborders = new LengthAwarePaginator($suborder, count($sub_orders), 15, null, array('path' => Paginator::resolveCurrentPath()));
        }
        return view('operation.orderHasDelivered')->with(['suborders' => $suborders]);
    }

    /*
     *
     * 渲染需要填二段物流的已发货
     *
     */
    public function hasSecondaryDelivered(Request $request)
    {
        $subs = SubOrder::where('sub_order_state', 601)->get()->sortByDesc('updated_at');
        $sub_orders = $subs->filter(function ($sub_order) {
            if (Cache::get('suborder:' . $sub_order->sub_order_id . ':secondaryDeliver'))
             {
                return $sub_order;
            }
        });
        $page = $request->page;
        $suborder = $sub_orders->forPage($page, 15);
        $suborders = new LengthAwarePaginator($suborder, count($sub_orders), 15, null, array('path' => Paginator::resolveCurrentPath()));
        return view('operation.orderSecondaryDeliver')->with(['suborders' => $suborders]);
    }

    /*
     *
     * 渲染已完成页
     *
     */
    public function hasFinished(Request $request)
    {
        $subs = SubOrder::where('sub_order_state', 301)->get()->sortByDesc('updated_at');
        $sub_orders = $subs->filter(function ($sub_order) {
            if ($sub_order->operator_id == Auth::user()->employee->employee_id) {
                return $sub_order;
            }
        });
        $page = $request->page;
        $suborder = $sub_orders->forPage($page, 15);
        $operator = Auth::user()->employee;
        if ($operator->op_level > 3) {
            $suborders = $this->suborder->getAllHasFinishedOrderWithPaginate(15);
        } elseif ($operator->op_level == 3) {
            $suborders = new LengthAwarePaginator($suborder, count($sub_orders), 15, null, array('path' => Paginator::resolveCurrentPath()));
        }
        return view('operation.orderHasFinished')->with(['suborders' => $suborders]);
    }

    /*
     *
     * 渲染拒单待分配页
     *
     */
    public function orderSellerAssign(Request $request)
    {
        $subs = SubOrder::where('sub_order_state', 241)->orWhere('sub_order_state', 541)->get()->sortByDesc('updated_at');
        $sub_orders = $subs->filter(function ($sub_order) {
            if ($sub_order->operator_id == Auth::user()->employee->employee_id) {
                return $sub_order;
            }
        });
        $page = $request->page;
        $suborder = $sub_orders->forPage($page, 15);
        $operator = Auth::user()->employee;
        if ($operator->op_level > 3) {
            $suborders = $this->suborder->getAllSellerAssignOrderWithPaginate(15);
        } elseif ($operator->op_level == 3) {
            $suborders = new LengthAwarePaginator($suborder, count($sub_orders), 15, null, array('path' => Paginator::resolveCurrentPath()));
        }
        return view('operation.orderSellerAssign')->with(['suborders' => $suborders]);
    }

    /*
     *
     * 渲染待审核TAB
     *
     */
    public function showAuditing()
    {
        $suborders = $this->suborder->getAllAuditingOrderWithPaginate(15);
        return view('operation.auditing')->with(['suborders' => $suborders]);
    }

    /*
     *
     * 后台订单管理已完成页
     *
     */
    public function getClosedOrder()
    {
        $suborders = $this->suborder->getAllClosedOrderWithPaginate(15);
        return view('operation.orderClosed')->with(['suborders' => $suborders]);
    }

    /*
     *
     * 将需求生成商品
     *
     */
    public function createItem(Request $request)
    {
        $input_price = $request->price;
        $input_title = $request->itemTitle;
        $input_pics = json_decode($request->pic_urls);
        $input_des = $request->description;
        $input_number = $request->number;
        $detail_id = $request->requirementDetailId;
        $memo = $request->operatingNotes;
        $detail = RequirementDetail::find($detail_id);
        if ($input_pics == ["/image/DefaultPicture.jpg"]) {
            $input_pics = [];
        }
        $requirement_id = $detail->requirement_id;
        $requirement = $this->requirement->getById($requirement_id);
        $publisher_id = $requirement->operator_id;
        $item = $this->item->create(array('title' => $input_title, 'is_available' => true,
            'pic_urls' => $input_pics, 'price' => $input_price, 'item_type' => 1, 'country_id' => 11), $publisher_id, null, true,
            array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number, 'sku_price' => $input_price)),
            array('hlj_buyer_description' => $input_des, 'number' => $input_number,
                'hlj_admin_response_information' => $memo
            ));
        $this->requirement->createRelation($requirement, $item->item_id);
        $detail->item_id = $item->item_id;
        $detail->state = 1;
        $detail->save();
        LogTest::writeTestLog(Auth::user()->employee->real_name . '生成' . $input_number . '件新商品', ['需求号' => $requirement->requirement_number, '商品ID' => $item->item_id, '商品名称' => $input_title, '单价' => $input_price, '运营备注' => $memo]);
        return redirect(url('operator/generateItems/' . $requirement_id));

    }


    /*
     *
     * 更新生成的商品
     *
     */
    public function updateCreatedItem(Request $request)
    {
        $input_price = $request->price;
        $input_title = $request->itemTitle;
        $input_pics = json_decode($request->pic_urls);
        $input_des = $request->description;
        $input_number = $request->number;
        $memo = $request->operatingNotes;
        $item_id = $request->itemId;
        $detail_id = $request->requirementDetailId;
        $detail = RequirementDetail::find($detail_id);
        $requirement_id = $detail->requirement_id;
        $item_update = $this->item->getById($item_id);
        if ($input_pics == ["/image/DefaultPicture.jpg"]) {
            $input_pics = [];
        }
        $this->item->updateItem($item_update, array('title' => $input_title,
            'pic_urls' => $input_pics, 'price' => $input_price), null,
            array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number, 'sku_price' => $input_price)),
            array('hlj_buyer_description' => $input_des, 'number' => $input_number,
                'hlj_admin_response_information' => $memo
            ));
        LogTest::writeTestLog(Auth::user()->employee->real_name . '更新了生成的商品' . $input_title, ['需求号' => $detail->requirement->requirement_number, '商品ID' => $item_id, '价格' => $input_price, '数量' => $input_number, '标题' => $input_title, '图片' => $input_pics, '运营备注' => $memo]);
        return redirect(url('operator/generateItems/' . $requirement_id));

    }

    /*
     *
     * 删除生成的商品或子需求
     *
     */
    public function deleteCreatedItem(Request $request)
    {
        $detail_id = $request->requirementDetailId;
        $requirement_detail = RequirementDetail::find($detail_id);
        $requirement_id = $requirement_detail->requirement_id;
        $item_id = $requirement_detail->item_id;
        $requirement = $this->requirement->getById($requirement_id);

        if ($item_id != 0) {
            $item = $this->item->getById($item_id);
            $this->requirement->deleteRelation($requirement, $item_id);
            $this->item->deleteItem($item);
            $item->detail_positive->is_available = false;
            $item->detail_positive->save();
        }

        $requirement_detail->is_available = false;
        $requirement_detail->save();
        $count = 0;
        LogTest::writeTestLog(Auth::user()->employee->real_name . '删除了生成的商品', ['需求号' => $requirement->requirement_number, '商品名称' => $requirement_detail->title]);
        foreach ($requirement->requirementDetails as $detail) {
            if ($detail->is_available == false) {
                $count += 1;
            }
        }
        if ($count == count($requirement->requirementDetails)) {
            $requirement->state = 431;
            $requirement->save();
            LogTest::writeTestLog(Auth::user()->employee->real_name . '将需求' . $requirement->requirement_number . '置为了无效', ['需求号' => $requirement->requirement_number]);
            return redirect(url('operator/waitResponse'));
        }

        return redirect(url('operator/generateItems/' . $requirement_id));

    }


    /*
     *
     * 生成逻辑主订单
     *
     */
    public function createMainOrder(Request $request)
    {
        $requirementId = $request->requirement_id;
        $requirement_id = Requirement::where('requirement_number', $requirementId)->first()->requirement_id;
        $requirement = $this->requirement->getById($requirement_id);
        $hlj_id = $requirement->hlj_id;
        $mainOrder = $this->mainOrder->createMainOrder(array('main_order_state' => 101), $hlj_id);
        $id = $mainOrder->main_order_id;
        $requirement->main_order_id = $id;
        $requirement->state = 201;
        $requirement->save();
        $main = $this->mainOrder->getById($id);
        $count = count($requirement->items);
        $sub = 0;
        for ($i = 0; $i < $count; $i++) {
            $main->items()->attach($requirement->items[$i]->item_id);
        }
        for ($i = 0; $i < $count; $i++) {
            $price = $main->items[$i]->skus->first()->sku_price;
            $number = $main->items[$i]->skus->first()->sku_inventory;
            $sub = $sub + ($price * $number);
        }
        $this->mainOrder->updateMainOrder($main, array('main_order_price' => $sub));
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将需求生成为逻辑主订单', ['需求号' => $requirement->requirement_number, '主订单ID' => $id]);
        return response()->json(url('/operator/splitOrder/' . $id));
    }

    /*
     *
     * 待生成商品TAB将需求置为无效
     *
     */
    public function invalidRequirement(Request $request)
    {
        $requirementId = $request->requirementId;
        $requirement_id = Requirement::where('requirement_number', $requirementId)->first()->requirement_id;
        $requirement = $this->requirement->getById($requirement_id);
        $hlj_id = $requirement->hlj_id;
        $this->requirement->updateStateToCancelRequirement($requirement);
        $temp = $requirement->requirementDetails;
        $count = count($temp);
        $title = '';
        for ($i = 0; $i < $count; $i++) {
            $temp[$i]->is_available = false;
            $title .= $temp[$i]->title . '；';
            $temp[$i]->save();
        }
        $title = rtrim($title, '；');
        if (mb_strlen($title) > 12) {
            $title_send = mb_substr($title, 0, 12) . '...';
        } else {
            $title_send = $title;
        }
        $open_id = User::find($hlj_id)->openid;
        $temp_item = $requirement->items;
        $count_item = count($temp_item);
        for ($i = 0; $i < $count_item; $i++) {
            $temp_item[$i]->is_available = false;
            $item_id = $temp_item[$i]->item_id;
            $item = $this->item->getById($item_id);
            $item->detail_positive->is_available = false;
            $item->detail_positive->save();
            $this->requirement->deleteRelation($requirement, $item_id);
            $temp_item[$i]->save();
        }
        $notice = new WXNotice();
        $notice->requestCanceled($open_id, $requirement->requirement_number, $title_send, '未报价');
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将需求' . $requirement->requirement_number . '置为了无效', ['需求号' => $requirement->requirement_number]);
        return response()->json(1);
    }

    /*
     *
     * 需求生成商品页
     *
     */
    public function getGenerateItemsPage(Request $request)
    {
        $requirement = $this->requirement->getById($request->requirementId);
        $requirement->user;
        $operator_id = Auth::user()->employee->employee_id;
        if ($requirement->operator_id != $operator_id && Auth::user()->employee->op_level <= 3) {
            abort(411);
        } else {
            return view('operation.requirementDetail')->with(['requirement' => $requirement, 'operator' => Auth::user()]);
        }
    }

    /*
     *
     * 领取需求页领取需求按钮
     *
     */
    public function editRequirement(Request $request)
    {
        $requirementId = $request->requirementId;
        $requirement_id = Requirement::where('requirement_number', $requirementId)->first()->requirement_id;
        $requirement = $this->requirement->getById($requirement_id);
        $requirement->user;
        return view('operation.waitCollectRequirement')->with(['requirement' => $requirement]);
    }

    /*
     *
     * 运营领取需求
     *
     */
    public function acceptRequirement(Request $request)
    {
        $requirementId = $request->requirementId;
        $operator_id = Auth::user()->employee->employee_id;
        $requirement_id = Requirement::where('requirement_number', $requirementId)->first()->requirement_id;
        $requirement = $this->requirement->getById($requirement_id);
        if ($requirement->operator_id != 0) {
            abort(421);
        } else {
            if (Auth::user()->employee->op_level >= 3) {
                $requirement->operator_id = $operator_id;
                $requirement->save();
                LogTest::writeTestLog(Auth::user()->employee->real_name . '领取了一个需求', ['需求号:' => $requirement->requirement_number, '国家:' => $requirement->country->name]);
//            return redirect(url('operator/waitResponse'));
                return redirect(url('operator/generateItems/' . $requirement_id));
            }
        }
    }

    /*
     *
     * 渲染需求管理全部TAB
     *
     */
    public function getAllRequirement(Request $request)
    {
        $requirement_all = Requirement::all()->diff(Requirement::where('state', 301)->get())->sortBy('state');
        $requirementsAll = $requirement_all->filter(function ($requirement) {
            if (($requirement->operator_id != 0 && $requirement->operator_id == Auth::user()->employee->employee_id) || (Auth::user()->employee->op_level > 3) || ($requirement->operator_id == 0)) {
                return $requirement;
            }
        });
        $page = $request->page;
        $requirement = $requirementsAll->forPage($page, 15);
        $requirements = new LengthAwarePaginator($requirement, count($requirementsAll), 15, null, array('path' => Paginator::resolveCurrentPath()));
        return view('operation.requirementGetAll')->with(['requirements' => $requirements]);
    }

    /*
     *
     * 渲染订单管理全部TAB
     *
     */
    public function getAllOrders(Request $request)
    {
        $order_all = SubOrder::all()->diff(SubOrder::where('sub_order_state', 101)->get())->sortByDesc('updated_at');
        $ordersAll = $order_all->filter(function ($sub_order) {
            if (($sub_order->operator_id == Auth::user()->employee->employee_id) || (Auth::user()->employee->op_level > 3)) {
                return $sub_order;
            }
        });
        $page = $request->page;
        $order = $ordersAll->forPage($page, 15);
        $orders = new LengthAwarePaginator($order, count($ordersAll), 15, null, array('path' => Paginator::resolveCurrentPath()));
        return view('operation.orderGetAll')->with(['suborders' => $orders]);
    }

    /*
     *
     * 获取该国买手信息
     *
     */
    public function getBuyer(Request $request)
    {
        $sellers = $this->seller->getSellerByCountry($request->countryId);
        $id_json = array();
        $name_json = array();
        $pinyin_json = array();
        $abb_json = array();
        foreach ($sellers as $seller) {
            array_push($id_json, $seller->seller_id);
            array_push($name_json, $seller->real_name);
            array_push($pinyin_json, $seller->name_pinyin);
            array_push($abb_json, $seller->name_abbreviation);
        }
        $response = [$id_json, $name_json, $pinyin_json, $abb_json];
        return response()->json($response);
    }

    /*
     *
     * 待拆单TAB删除需求
     *
     */
    public function deleteRequirement(Request $request)
    {
        $requirementId = $request->requirementId;
        $requirement = Requirement::where('requirement_number', $requirementId)->first();
        $hlj_id = $requirement->hlj_id;
        $mainOrder = $requirement->main_order;
        $details = $requirement->requirementDetails;
        foreach ($details as $detail) {
            $detail->is_available = false;
            $detail->save();
        }
        $temp_item = $requirement->items;
        $count_item = count($temp_item);
        for ($i = 0; $i < $count_item; $i++) {
            $temp_item[$i]->is_available = false;
            $item_id = $temp_item[$i]->item_id;
            $item = $this->item->getById($item_id);
            $item->detail_positive->is_available = false;
            $item->detail_positive->save();
            $this->requirement->deleteRelation($requirement, $item_id);
            $temp_item[$i]->save();
        }
        if (!empty($mainOrder)) {
            $count = count($mainOrder->subOrders);
            if ($count != 0) {
                for ($i = 0; $i < $count; $i++) {
                    $sub_id = $mainOrder->subOrders[$i]->sub_order_id;
                    $subOrder = $this->suborder->getById($sub_id);
                    $count_item = count($subOrder->items);
//                    for ($j = 0; $j < $count_item; $j++) {
//                        $subOrder->items()->detach($subOrder->items[$j]->item_id);
//                    }
                    $this->suborder->deleteSubOrder($subOrder);
                }
            }
            $count_mainOrder = count($mainOrder->items);
            for ($i = 0; $i < $count_mainOrder; $i++) {
//                $mainOrder->items()->detach($mainOrder->items[$i]->item_id);
                $item_delete = $this->item->getById($mainOrder->items[$i]->item_id);
                $this->item->deleteItem($item_delete);
            }

            $this->mainOrder->deleteMainOrder($mainOrder);
        }
        $requirement->state = 431;
        $requirement->save();
        $title = '';
        $count = count($details);
        for ($i = 0; $i < $count; $i++) {
            $details[$i]->is_available = false;
            $title .= $details[$i]->title . '；';
            $details[$i]->save();
        }
        $title = rtrim($title, '；');
        if (mb_strlen($title) > 12) {
            $title_send = mb_substr($title, 0, 12) . '...';
        } else {
            $title_send = $title;
        }
        $open_id = User::find($hlj_id)->openid;
        $notice = new WXNotice();
        $notice->requestCanceled($open_id, $requirement->requirement_number, $title_send, '未报价');
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将需求' . $requirement->requirement_number . '置为了无效', ['需求号' => $requirement->requirement_number]);
        return response()->json(1);
    }

    /*
     *
     * 运营取消订单
     *
     */
    public function cancelOrder(Request $request)
    {
        $suborder = SubOrder::where('sub_order_number',$request->orderId)->first();
        $buyer_openid = User::find($suborder->buyer_id)->openid;
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
            $price = $item->price * $item_count + $suborder->postage;
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
        $suborderDeleted = $this->suborder->deleteSubOrderByUser($suborder);
        if ($itemSaved && $skuSaved && $suborderDeleted) {
            DB::commit();
            if (mb_strlen($title) > 12) {
                $title = mb_substr($title, 0, 12) . '...';
            } else {
                $title = rtrim($title, '；');
            }
            if ($suborder->buyer->is_subscribed == 1) {
                $notice = new WXNotice();
                $notice->orderCanceled($buyer_openid, $suborder->sub_order_number, $title, sprintf('%.2f', $price));

            }
            return redirect(url('/operator/waitPay'));
        } else {
            DB::rollback();
            return redirect(url('/operator/waitPay'));
        }

    }

    /*
     *
     * 更改买手
     *
     */
    public function updateSeller(Request $request)
    {
        $suborder = $this->suborder->getById($request->subOrderId);
        $country_id = $request->buyer_country;
        $seller_id = $request->buyer;
        $seller = Seller::find($seller_id);
        $old_seller_id = $request->oldSellerId;
        $old_seller = Seller::find($old_seller_id);
        $old_seller->seller_refuse_orders_num += 1;
        $old_seller->save();
        $seller->seller_receive_orders_num += 1;
        $seller->save();
        $this->suborder->updateSubOrder($suborder, array('country_id' => $country_id, 'seller_id' => $seller_id));
        $notice = new WXNotice();
        LogTest::UpdateSellerLog(Auth::user()->employee->real_name . '更换了买手', ['订单号' => $suborder->sub_order_number, '原买手' => $request->oldSellerName, '新买手' => $seller->real_name, '更换原因' => $request->reason]);
        if ($suborder->sub_order_state == 541) {
            $title_mail = '';
            $title = '';
            $items = $suborder->items;
            if (count($items) > 1) {
                $title_mail = $items[0]->title . '...';
            } else {
                $title_mail = $items[0]->title;
            }
            foreach ($items as $item) {
                $title .= $item->title . '；';
            }
            $title = rtrim($title, '；');
            if (count($title) > 12) {
                $title_notice = mb_substr($title, 0, 12);
            } else {
                $title_notice = $title;
            }
            $email = $seller->user->email;
            $receiver_name = $suborder->receivingAddress->receiver_name;
            $receiver_mobile = $suborder->receivingAddress->receiver_mobile;
            $receiver_zip_code = $suborder->receivingAddress->receiver_zip_code;
            $province_code = $suborder->receivingAddress->first_class_area;
            $city_code = $suborder->receivingAddress->second_class_area;
            $county_code = $suborder->receivingAddress->third_class_area;
            $street_address = $suborder->receivingAddress->street_address;
            $province_level = $this->regionInstance->getRegionByCode($province_code)->name;
            if ($city_code == 1) {
                $city_level = "";
            } else {
                $city_level = $this->regionInstance->getRegionByCode($city_code)->name;
            }
            if ($county_code == 1) {
                $county_level = "";
            } else {
                $county_level = $this->regionInstance->getRegionByCode($county_code)->name;
            }
            $receiving_address = $province_level . $city_level . $county_level . $street_address;
            $notice->tellSellerDeliver($seller->user->openid, $suborder->sub_order_number,
                $title_notice, sprintf('%.2f', $suborder->sub_order_price));
            \Event::fire(new MailToSellerForDeliverEvent($email, $suborder->sub_order_number,
                $title_mail, sprintf('%.2f', $suborder->sub_order_price), $suborder->payment_time,
                $receiver_name, $receiver_mobile, $receiving_address, $receiver_zip_code, time(), 1));
            $suborder->sub_order_state = 501;
            $suborder->save();
            return redirect(url('/operator/waitDelivery'));
        }
        if ($suborder->sub_order_state == 241) {
            $suborder->sub_order_state = 201;
            $suborder->save();
            return redirect(url('/operator/waitPay'));
        } else {
            if ($suborder->sub_order_state == 501) {
                $title_mail = '';
                $title = '';
                $items = $suborder->items;
                if (count($items) > 1) {
                    $title_mail = $items[0]->title . '...';
                } else {
                    $title_mail = $items[0]->title;
                }
                foreach ($items as $item) {
                    $title .= $item->title . '；';
                }
                $title = rtrim($title, '；');
                if (count($title) > 12) {
                    $title_notice = mb_substr($title, 0, 12);
                } else {
                    $title_notice = $title;
                }
                $email = $seller->user->email;
                $receiver_name = $suborder->receivingAddress->receiver_name;
                $receiver_mobile = $suborder->receivingAddress->receiver_mobile;
                $receiver_zip_code = $suborder->receivingAddress->receiver_zip_code;
                $province_code = $suborder->receivingAddress->first_class_area;
                $city_code = $suborder->receivingAddress->second_class_area;
                $county_code = $suborder->receivingAddress->third_class_area;
                $street_address = $suborder->receivingAddress->street_address;
                $province_level = $this->regionInstance->getRegionByCode($province_code)->name;
                if ($city_code == 1) {
                    $city_level = "";
                } else {
                    $city_level = $this->regionInstance->getRegionByCode($city_code)->name;
                }
                if ($county_code == 1) {
                    $county_level = "";
                } else {
                    $county_level = $this->regionInstance->getRegionByCode($county_code)->name;
                }
                $receiving_address = $province_level . $city_level . $county_level . $street_address;
                $notice->tellSellerDeliver($seller->user->openid, $suborder->sub_order_number,
                    $title_notice, sprintf('%.2f', $suborder->sub_order_price));
                \Event::fire(new MailToSellerForDeliverEvent($email, $suborder->sub_order_number,
                    $title_mail, sprintf('%.2f', $suborder->sub_order_price), $suborder->payment_time,
                    $receiver_name, $receiver_mobile, $receiving_address, $receiver_zip_code, time(), 1));
            }
            return back();
        }
    }

    /*
     *
     * 填写物流信息页并切换到待审核状态
     *
     */
    public function createDeliveryInfo(Request $request)
    {
        if ($request->pinyin == "") {
            $url = "http://www.kuaidi100.com/";
            $deliveryInfo = $this->deliveryInfo->createDelivery(array('sub_order_id' => $request->sub_order_id,
                'delivery_order_number' => $request->express_number, 'delivery_company_id' => $request->express,
                'delivery_company_info' => $request->otherExpress, 'delivery_related_url' => $url));
        } else {
            $com = $request->pinyin;
            $url = "http://m.kuaidi100.com/index_all.html?type=" . $com . "&postid=" . $request->express_number;
            $deliveryInfo = $this->deliveryInfo->createDelivery(array('sub_order_id' => $request->sub_order_id,
                'delivery_order_number' => $request->express_number, 'delivery_company_id' => $request->express,
                'delivery_company_info' => $request->otherExpress, 'delivery_related_url' => $url));
        }
        $id = $deliveryInfo->delivery_info_id;
        $suborder = $this->suborder->getById($request->sub_order_id);
        $suborder->sub_order_state = 521;
        $suborder->delivery_info_id = $id;
        $suborder->delivery_time = date('Y-m-d H:i:s');
        if ($request->secondaryDeliver == 1) {
            Cache::forever("suborder:" . $suborder->sub_order_id . ":secondaryDeliver", 1);
            $deliveryInfo->is_second_phase = true;
            $deliveryInfo->save();
        }
        $items = $suborder->items;
        $title = '';
        foreach ($items as $item) {
            $title .= $item->title . '；';
        }
        $title = rtrim($title, '；');
        if (mb_strlen($title) > 8) {
            $title_send = mb_substr($title, 0, 8) . '...';
        } else {
            $title_send = $title . '。';
        }
        if (mb_strlen($title) > 12) {
            $title_notice = mb_substr($title, 0, 12) . '...';
        } else {
            $title_notice = $title;
        }
        $hlj_id = $suborder->mainOrder->hlj_id;
        $user = User::find($hlj_id);
//        \Event::fire(new DeliveryNotification($user,$suborder,$items));
        $mobile = $user->mobile;
        $buyer_openid = User::find($hlj_id)->openid;
        $notice = new WXNotice();
        $notice->deliverItems($buyer_openid, $suborder->sub_order_number,
            $title_notice, $suborder->sub_order_price - $suborder->refund_price, $suborder->sub_order_id);
        LeanCloud::sellerDeliverItemsSMS($mobile, $title_send, $suborder->operator->user->mobile);
        LogTest::writeTestLog(Auth::user()->employee->real_name . '填写了物流信息', ['子订单号' => $suborder->sub_order_number]);
        $suborder->save();
        return redirect(url('/operator/isAuditing'));
    }

    /*
     *
     * 运营审核通过切换为已发货状态
     *
     */
    public function commitToHasDelivered(Request $request)
    {
        $subOrderNumber = $request->subOrderNumber;
        $subOrderId = SubOrder::where('sub_order_number', $subOrderNumber)->first()->sub_order_id;
        $suborder = $this->suborder->getById($subOrderId);
        $items = $suborder->items;
        $title = '';
        foreach ($items as $item) {
            $title .= $item->title . '；';
        }
        $title = rtrim($title, '；');
        if (mb_strlen($title) > 8) {
            $title_send = mb_substr($title, 0, 8) . '...';
        } else {
            $title_send = $title . '。';
        }
        if (mb_strlen($title) > 12) {
            $title_notice = mb_substr($title, 0, 12) . '...';
        } else {
            $title_notice = $title;
        }
        $user = Auth::user();
        $hlj_id = $suborder->mainOrder->hlj_id;
        $mobile = User::find($hlj_id)->mobile;
        $buyer_openid = User::find($hlj_id)->openid;
        $seller_id = $suborder->seller_id;
        $seller = Seller::find($seller_id);
        $seller->seller_success_orders_num += 1;
        $seller->seller_success_incoming += $suborder->sub_order_price - $suborder->refund_price;
        $seller_openid = $suborder->seller->user->openid;
        $suborder->sub_order_state = 601;
        $suborder->transfer_price = $suborder->sub_order_price - $suborder->refund_price;
        $suborder->audit_passed_time = date('Y-m-d H:i:s');
        $suborder->save();
        $seller->save();
        LogTest::writeTestLog(Auth::user()->employee->real_name . '通过了订单' . $subOrderNumber . '物流的审核', ['子订单号' => $subOrderNumber]);
//        LeanCloud::sellerDeliverItemsSMS($mobile,$title_send,$user->mobile);
        $notice = new WXNotice();
        $notice->sellerCanWithdraw($seller_openid, $title_notice, $subOrderNumber);
//        $notice->deliverItems($buyer_openid,$subOrderNumber,$title_notice,$suborder->transfer_price);
        return redirect(url('/operator/isAuditing/?page=1'));
    }

    /**
     *
     * 运营填写第二段物流
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     */
    public function commitToOverseaDelivered(Request $request)
    {
        $subOrderNumber = $request->subOrderNumber;
        $subOrderId = SubOrder::where('sub_order_number', $subOrderNumber)->first()->sub_order_id;
        $suborder = $this->suborder->getById($subOrderId);
        $user = Auth::user();
        if ($request->pinyin == "") {
            $url = "http://www.kuaidi100.com/";
        } else {
            $com = $request->pinyin;
            $url = "http://m.kuaidi100.com/index_all.html?type=" . $com . "&postid=" . $request->express_number;
        }
        $delivery_info['sub_order_id'] = $subOrderId;
        $delivery_info['delivery_order_number'] = $request->express_number;
        $delivery_info['delivery_company_id'] = $request->express;
        $delivery_info['delivery_company_info'] = $request->otherExpress;
        $delivery_info['delivery_related_url'] = $url;
        Cache::forever('suborder:' . $subOrderId . ':additional', serialize($delivery_info));
        $d_info = $suborder->deliveryInfo;
        $d_info->second_phase_info = json_encode($delivery_info);
        $d_info->save();
        if (Cache::get("suborder:" . $suborder->sub_order_id . ":secondaryDeliver")) {
            Cache::forget("suborder:" . $suborder->sub_order_id . ":secondaryDeliver", 1);
        }
        LogTest::writeTestLog($user->employee->real_name . '填写了第二段物流信息', ['子订单号' => $suborder->sub_order_number]);
        return redirect(url('/operator/hasSecondaryDelivered'));
    }

    /*
     *
     * 运营审核未通过切换回待发货状态
     *
     */
    public function commitToUndelivered(Request $request)
    {
        $subOrderNumber = $request->subOrderNumber;
        $subOrderId = SubOrder::where('sub_order_number', $subOrderNumber)->first()->sub_order_id;
        $suborder = $this->suborder->getById($subOrderId);
        $suborder->sub_order_state = 501;
        $seller_openid = $suborder->seller->user->openid;
        $items = $suborder->items;
        $title = '';
        foreach ($items as $item) {
            $title .= $item->title . '；';
        }
        $title = rtrim($title, '；');
        if (mb_strlen($title) > 12) {
            $title_notice = mb_substr($title, 0, 12) . '...';
        } else {
            $title_notice = $title;
        }
        LogTest::writeTestLog(Auth::user()->employee->real_name . '未通过订单' . $subOrderNumber . '物流的审核', ['子订单号' => $subOrderNumber]);
        $suborder->save();
        $notice = new WXNotice();
        $notice->sellerCannotWithdraw($seller_openid, $title_notice, $subOrderNumber);
        return redirect(url('/operator/waitDelivery'));
    }

    /*
     *
     * 确认收货
     *
     */
    public function commitToHasFinished(Request $request)
    {
        $subOrderNumber = $request->subOrderNumber;
        $subOrderId = SubOrder::where('sub_order_number', $subOrderNumber)->first()->sub_order_id;
        $suborder = $this->suborder->getById($subOrderId);
        $suborder->sub_order_state = 301;
        $suborder->save();
        $buyer = Buyer::where('hlj_id', $suborder->mainOrder->hlj_id)->first();
        $buyer->buyer_success_orders_num += 1;
        $buyer->buyer_actual_paid += $suborder->sub_order_price - $suborder->refund_price;
        $buyer->save();
        LogTest::writeTestLog(Auth::user()->employee->real_name . '经与买家沟通帮其确认了收货', ['子订单号' => $subOrderNumber]);
        return redirect(url('/operator/hasFinished'));
    }

    /*
     *
     * 需求管理查询
     *
     */
    public function searchRequirement(Request $request)
    {
        if (($request->orderTime2 == $request->orderTime) && $request->orderTime != '') {
            $request->orderTime2 = date('Y-m-d H:i:s', strtotime($request->orderTime2) + 24 * 60 * 60);
        }
        $duration = strtotime($request->orderTime2) - strtotime($request->orderTime);
        if (($request->buyerPhone != '') || ($request->buyerEmail != '')) {
            $user = User::where('mobile', $request->buyerPhone)->orWhere('email', $request->buyerEmail)->first();
            if (count($user) != 0) {
                $hlj_id = $user->hlj_id;
            } else {
                abort(431);
            }
            $state = 0;
        } else {
            $state = 1;
        }
        if ($request->requirement_id != '') {
            $requirements = Requirement::where('requirement_number', $request->requirement_id)->get();
        } else {
            if ($request->operator_id == 0) {
                if (($request->country_id == 0) && $state == 0 && $duration != 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('created_at', '<', $request->orderTime2)->where('created_at', '>', $request->orderTime)->get();
                } elseif (($request->country_id == 0) && $state == 0 && $duration == 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->get();
                } elseif (($request->country_id != 0) && $state == 0 && $duration == 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('country_id', $request->country_id)->get();
                } elseif (($request->country_id != 0) && $state == 0 && $duration != 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('created_at', '<', $request->orderTime2)
                        ->where('created_at', '>', $request->orderTime)->where('country_id', $request->country_id)->get();
                } elseif (($request->country_id == 0) && $state == 1 && $duration != 0) {
                    $requirements = Requirement::where('created_at', '<', $request->orderTime2)
                        ->where('created_at', '>', $request->orderTime)->get();
                } elseif (($request->country_id != 0) && $state == 1 && $duration != 0) {
                    $requirements = Requirement::where('created_at', '<', $request->orderTime2)
                        ->where('created_at', '>', $request->orderTime)->where('country_id', $request->country_id)->get();
                } elseif (($request->country_id != 0) && $state == 1 && $duration == 0) {
                    $requirements = Requirement::where('country_id', $request->country_id)->get();
                } elseif (($request->country_id == 0) && $state == 1 && $duration == 0) {
                    $requirements = Requirement::all();
                }
            } elseif ($request->operator_id != 0) {
                if (($request->country_id == 0) && $state == 0 && $duration != 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('created_at', '<', $request->orderTime2)->
                    where('created_at', '>', $request->orderTime)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id == 0) && $state == 0 && $duration == 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id != 0) && $state == 0 && $duration == 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('country_id', $request->country_id)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id != 0) && $state == 0 && $duration != 0) {
                    $requirements = Requirement::where('hlj_id', $hlj_id)->where('created_at', '<', $request->orderTime2)
                        ->where('created_at', '>', $request->orderTime)->where('country_id', $request->country_id)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id == 0) && $state == 1 && $duration != 0) {
                    $requirements = Requirement::where('created_at', '<', $request->orderTime2)
                        ->where('created_at', '>', $request->orderTime)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id != 0) && $state == 1 && $duration != 0) {
                    $requirements = Requirement::where('created_at', '<', $request->orderTime2)
                        ->where('created_at', '>', $request->orderTime)->where('country_id', $request->country_id)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id != 0) && $state == 1 && $duration == 0) {
                    $requirements = Requirement::where('country_id', $request->country_id)->where('operator_id', $request->operator_id)->get();
                } elseif (($request->country_id == 0) && $state == 1 && $duration == 0) {
                    $requirements = Requirement::where('operator_id', $request->operator_id)->get();
                }
            }
        }
        $requirementsAll = $requirements->diff(Requirement::where('state', 301)->get())->sortByDesc('updated_at');
//        $requirementsAll = $requirement_all->filter(function($requirement){
//            if(($requirement->operator_id!=0&&$requirement->operator_id == Auth::user()->hlj_id)||(Auth::user()->employee->op_level>3)||($requirement->operator_id==0)){
//                return $requirement;
//            }
//        });
        $page = $request->page;
        $requirement = $requirementsAll->forPage($page, 15);
        $requirements = new LengthAwarePaginator($requirement, count($requirementsAll), 15, null, array('path' => Paginator::resolveCurrentPath()));
        return view('operation.requirementSearched')->with(['requirements' => $requirements]);
    }

    /**
     * 需求管理页添加备注
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addRequirementMemo(Request $request)
    {
        $id = $request->requirementId;
        $requirement = $this->requirement->getById($id);
        RequirementMemo::create(['content' => $request->requirement_memo, 'hlj_id' => Auth::user()->hlj_id,
            'requirement_id' => $requirement->requirement_id, 'state' => $requirement->state]);
        return back();

    }

    /**
     * 订单管理页添加备注
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addOrderMemo(Request $request)
    {
        $id = $request->orderId;
        $suborder = $this->suborder->getById($id);
        SubOrderMemo::create(['content' => $request->order_memo, 'hlj_id' => Auth::user()->hlj_id,
            'sub_order_id' => $suborder->sub_order_id]);
        return back();
    }

    /**
     * 买手管理页添加备注
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSellerMemo(Request $request)
    {
        $id = $request->sellerId;
        $seller = $this->seller->getById($id);
        SellerMemo::create(['content' => $request->seller_remarks, 'hlj_id' => Auth::user()->hlj_id,
            'seller_id' => $seller->seller_id]);
        return back();
    }

    /**
     * 买家管理页添加备注
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addBuyerMemo(Request $request)
    {
        $id = $request->buyerId;
        $buyer = $this->buyer->getById($id);
        $hlj_id = Auth::user()->hlj_id;
        BuyerMemo::create(['content' => $request->seller_remarks, 'hlj_id' => $hlj_id,
            'buyer_id' => $buyer->buyer_id]);
        return back();
    }

    /**
     *
     * 删除运营，订单，买手，买家备注
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMemo(Request $request)
    {
        $id = $request->Id;
        $state = $request->state;
        if ($state == 1) {
            $requirementMemo = RequirementMemo::find($id);
            $requirementMemo->is_available = false;
            $requirementMemo->save();
            $requirementMemo->delete();

        }
        if ($state == 2) {
            $orderMemo = SubOrderMemo::find($id);
            $orderMemo->is_available = false;
            $orderMemo->save();
            $orderMemo->delete();
        }
        if ($state == 3) {
            $sellerMemo = SellerMemo::find($id);
            $sellerMemo->is_available = false;
            $sellerMemo->save();
            $sellerMemo->delete();
        }
        if ($state == 4) {
            $buyerMemo = BuyerMemo::find($id);
            $buyerMemo->is_available = false;
            $buyerMemo->save();
            $buyerMemo->delete();
        }
        return response()->json(1);
    }

    /**
     * 更改处理人
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateOperator(Request $request)
    {
        $requirementNumber = $request->requirement_id;
        $operator_id = $request->operator;
        $requirement_id = Requirement::where('requirement_number', $requirementNumber)->first()->requirement_id;
        $requirement = $this->requirement->getById($requirement_id);
        $requirement->operator_id = $operator_id;
        $requirement->save();
        if ($requirement->main_order && $requirement->main_order->subOrders) {
            $subs = $requirement->main_order->subOrders;
            foreach($subs as $sub) {
                $sub->operator_id = $operator_id;
                $sub->save();
            }
        }
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将需求' . $requirementNumber . '的处理人更改为' . Employee::find($operator_id)->real_name, ['需求号' => $requirementNumber, '新处理人' => Employee::find($operator_id)->real_name]);
        return redirect(url('operator/getAllRequirement'));
    }

    /**
     *
     * 订单状态更改处理人
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateOrderOperator(Request $request)
    {
        $orderNumber = $request->order_id;
        $operator_id = $request->operator;
        $order_id = SubOrder::where('sub_order_number', $orderNumber)->first()->sub_order_id;
        $suborder = $this->suborder->getById($order_id);
        $suborder->operator_id = $operator_id;
        $requirement = $suborder->mainOrder->requirement;
        if ($requirement->state != 301) {
            $requirement->operator_id = $operator_id;
            $requirement->save();
        }
        $suborder->save();
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将订单' . $orderNumber . '的处理人更改为' . User::find($operator_id)->employee->real_name, ['订单号' => $orderNumber, '新处理人' => User::find($operator_id)->employee->real_name]);
        return redirect(url('operator/getAllOrders'));
    }

    /**
     * 获取所有买手信息
     * @return $this
     */
    public function getSeller()
    {
        $sellers = $this->seller->getAllAvailableSellersWithPaginate(20);
        return view('operation.sellerManagement')->with(['sellers' => $sellers]);
    }

    /**
     * 买手信息详情页
     * @param Request $request
     * @return $this
     */
    public function getSellerDetail(Request $request)
    {
        $seller_id = $request->sellerId;
        $seller = $this->seller->getById($seller_id);
        return view('operation.sellerManagementDetail')->with(['seller' => $seller]);
    }

    /**
     * 将买手关进小黑屋
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function arrestSeller(Request $request)
    {

        $reason = $request->block_reason;
        $seller_id = $request->sellerId;
        $seller = $this->seller->getById($seller_id);
        $seller->is_available = false;
        $seller->save();
        SellerPrison::create(['reasons' => $reason, 'seller_id' => $seller_id]);
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将买手' . $seller->real_name . '拉入小黑屋', ['买手ID' => $seller_id]);
        return back();
    }

    /**
     * 将买手拉出小黑屋
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function freeSeller(Request $request)
    {
        $seller_id = $request->sellerId;
        $seller = $this->seller->getById($seller_id);
        $seller->is_available = true;
        $reason = $seller->sellerPrison;
        $reason->delete();
        $seller->save();
        LogTest::writeTestLog(Auth::user()->employee->real_name . '将买手' . $seller->real_name . '拉出小黑屋', ['买手ID' => $seller_id]);
        return back();
    }

    /**
     * 更改买手国家
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSellerCountry(Request $request)
    {
        $seller_id = $request->sellerId;
        $seller = $this->seller->getById($seller_id);
        $seller->country_id = $request->seller_country;
        $seller->save();
        return back();
    }

    /**
     * 添加微信号
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addWX_Number(Request $request)
    {
        if ($request->seller_weixinId != '') {
            $seller_id = $request->sellerId;
            $seller = $this->seller->getById($seller_id);
            $user = $seller->user;
            $user->wx_number = $request->seller_weixinId;
            $user->save();
        } elseif ($request->buyer_weixinId != '') {
            $buyer_id = $request->buyerId;
            $buyer = $this->buyer->getById($buyer_id);
            $user = $buyer->user;
            $user->wx_number = $request->buyer_weixinId;
            $user->save();
        }
        return back();
    }

    /**
     * 买手查询
     * @param Request $request
     * @return $this
     */
    public function searchSeller(Request $request)
    {
        if (($request->registerTime2 == $request->registerTime) && ($request->registerTime != '')) {
            $request->registerTime2 = date('Y-m-d H:i:s', strtotime($request->registerTime2) + 24 * 60 * 60);
        }
        $duration = strtotime($request->registerTime2) - strtotime($request->registerTime);
        if (($request->weixin_id != '') || ($request->sellerMobile != '') || ($request->sellerEmail != '')) {
            if ($request->weixin_id != '') {
                $hlj_id = User::where('wx_number', 'like', '%' . $request->weixin_id . '%')->orWhere('mobile', $request->sellerMobile)->orWhere('email', $request->sellerEmail)->first();
                if (count($hlj_id) != 0) {
                    $sellers = Seller::where('hlj_id', $hlj_id->hlj_id)->get();
                } else {
                    abort(431);
                }
            } else {
                $hlj_id = User::where('mobile', $request->sellerMobile)->orWhere('email', $request->sellerEmail)->first();
                if (count($hlj_id) != 0) {
                    $sellers = Seller::where('hlj_id', $hlj_id->hlj_id)->get();
                } else {
                    abort(431);
                }
            }
        } else {
            if ($request->sellerStates == 0) {
                if ($request->country_id != 0) {
                    if (($request->realname != '') && ($duration != 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('country_id', $request->country_id)->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->get();
                    } elseif (($request->realname != '') && ($duration == 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('country_id', $request->country_id)->get();
                    } elseif (($request->realname == '') && ($duration == 0)) {
                        $sellers = Seller::where('country_id', $request->country_id)->get();
                    } elseif (($request->realname == '') && ($duration != 0)) {
                        $sellers = Seller::where('country_id', $request->country_id)->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->get();
                    }
                } elseif ($request->country_id == 0) {
                    if (($request->realname != '') && ($duration != 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->get();
                    } elseif (($request->realname != '') && ($duration == 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->get();
                    } elseif (($request->realname == '') && ($duration != 0)) {
                        $sellers = Seller::whereBetween('created_at', [$request->registerTime, $request->registerTime2])->get();
                    } elseif (($request->realname == '') && ($duration == 0)) {
                        $sellers = Seller::all();
                    }
                }
            } else if ($request->sellerStates == 1) {
                if ($request->country_id != 0) {
                    if (($request->realname != '') && ($duration != 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('country_id', $request->country_id)->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', true)->get();
                    } elseif (($request->realname != '') && ($duration == 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('country_id', $request->country_id)->where('is_available', true)->get();
                    } elseif (($request->realname == '') && ($duration == 0)) {
                        $sellers = Seller::where('country_id', $request->country_id)->where('is_available', true)->get();
                    } elseif (($request->realname == '') && ($duration != 0)) {
                        $sellers = Seller::where('country_id', $request->country_id)->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', true)->get();
                    }

                } elseif ($request->country_id == 0) {
                    if (($request->realname != '') && ($duration != 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', true)->get();
                    } elseif (($request->realname != '') && ($duration == 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('is_available', true)->get();
                    } elseif (($request->realname == '') && ($duration != 0)) {
                        $sellers = Seller::whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', true)->get();
                    } elseif (($request->realname == '') && ($duration == 0)) {
                        $sellers = Seller::where('is_available', true)->get();
                    }
                }
            } else if ($request->sellerStates == 2) {
                if ($request->country_id != 0) {
                    if (($request->realname != '') && ($duration != 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', false)->get();
                    } elseif (($request->realname != '') && ($duration == 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('country_id', $request->country_id)->where('is_available', false)->get();
                    } elseif (($request->realname == '') && ($duration == 0)) {
                        $sellers = Seller::where('country_id', $request->country_id)->where('is_available', false)->get();
                    } elseif (($request->realname == '') && ($duration != 0)) {
                        $sellers = Seller::where('country_id', $request->country_id)->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', false)->get();
                    }
                } elseif ($request->country_id == 0) {
                    if (($request->realname != '') && ($duration != 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', false)->get();
                    } elseif (($request->realname != '') && ($duration == 0)) {
                        $sellers = Seller::where('real_name', 'like', '%' . $request->realname . '%')->where('is_available', false)->get();
                    } elseif (($request->realname == '') && ($duration != 0)) {
                        $sellers = Seller::whereBetween('created_at', [$request->registerTime, $request->registerTime2])->where('is_available', false)->get();
                    } elseif (($request->realname == '') && ($duration == 0)) {
                        $sellers = Seller::where('is_available', false)->get();
                    }
                }
            }
        }
        $sellersAll = $sellers->sortByDesc('seller_success_orders_num');
        $page = $request->page;
        $seller = $sellersAll->forPage($page, 20);
        $sellers = new LengthAwarePaginator($seller, count($sellersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.sellerSearched')->with(['sellers' => $sellers]);

    }

    /**
     * 打款管理页待确认打款金额
     * @param Request $request
     * @return $this
     */
    public function waitEnsureCapital(Request $request)
    {
        $sub_orders = SubOrder::where('withdraw_state', 1)->get()->sortByDesc('updated_at');
        $subOrdersAll = $sub_orders->filter(function ($suborder) {
            if (($suborder->operator_id == Auth::user()->employee->employee_id)) {
                return $suborder;
            }
        });
        $page = $request->page;
        $suborder = $subOrdersAll->forPage($page, 20);
        $subOrders = new LengthAwarePaginator($suborder, count($subOrdersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));

        return view('operation.remittancePending')->with(['subOrders' => $subOrders]);
    }

    /**
     * 打款管理页待打款
     * @param Request $request
     * @return $this
     */
    public function waitTransfer(Request $request)
    {
        $sub_orders = SubOrder::where('withdraw_state', 2)->get()->sortByDesc('updated_at');
        $subOrdersAll = $sub_orders->filter(function($suborder){
            if(($suborder->operator_id == Auth::user()->employee->employee_id || Auth::user()->employee->employee_id == 6))
            {
                return  $suborder;
            }
        });
        $page = $request->page;
        $suborder = $subOrdersAll->forPage($page, 20);
        $subOrders = new LengthAwarePaginator($suborder, count($sub_orders), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.remittancing')->with(['subOrders' => $subOrders]);
    }

    /**
     * 打款管理页已打款
     * @param Request $request
     * @return $this
     */
    public function hasTransferred(Request $request)
    {
        $sub_orders = SubOrder::where('withdraw_state', 3)->get()->sortByDesc('updated_at');
        $subOrdersAll = $sub_orders->filter(function ($suborder) {
            if (($suborder->operator_id == Auth::user()->employee->employee_id) || (Auth::user()->employee->op_level > 3)) {
                return $suborder;
            }
        });
        $page = $request->page;
        $suborder = $subOrdersAll->forPage($page, 20);
        $subOrders = new LengthAwarePaginator($suborder, count($subOrdersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));

        return view('operation.remittanced')->with(['subOrders' => $subOrders]);
    }

    /**
     * 打款管理页全部TAB
     * @param Request $request
     * @return $this
     */
    public function allCapital(Request $request)
    {
        $sub_orders = SubOrder::where('withdraw_state', 1)->orWhere('withdraw_state', 2)->orWhere('withdraw_state', 3)->get()->sortByDesc('updated_at');
        $subOrdersAll = $sub_orders->filter(function ($suborder) {
            if (($suborder->operator_id == Auth::user()->employee->employee_id) || (Auth::user()->employee->op_level > 3)) {
                return $suborder;
            }
        });
        $page = $request->page;
        $suborder = $subOrdersAll->forPage($page, 20);
        $subOrders = new LengthAwarePaginator($suborder, count($subOrdersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));

        return view('operation.remittanceManagement')->with(['subOrders' => $subOrders]);
    }

    /**
     * 运营童鞋确认打款金额
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function ensureCapital(Request $request)
    {
        $sub_id = SubOrder::where('sub_order_number', $request->order_id)->first()->sub_order_id;
        $suborder = $this->suborder->getById($sub_id);
        if ($request->changeRefundsReason != '') {
            TransferReason::create(['sub_order_id' => $sub_id, 'reason' => $request->changeRefundsReason]);
        }
        $suborder->transfer_price = $request->order_price;
        $suborder->withdraw_state = 2;
        $suborder->save();
        return redirect(url('/operator/waitEnsureCapital/?page=1'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * 运营童鞋确认打款将钱打给买手
     *
     */
    public function hasTransfer(Request $request)
    {
        if (!$request->payment_id || Auth::user()->hlj_id != 6) {
            abort('412');
        } else {
            $sub_id = SubOrder::where('sub_order_number', $request->subOrderId)->first()->sub_order_id;
            $suborder = $this->suborder->getById($sub_id);
            $suborder->withdraw_state = 3;
            $suborder->payment_methods_id = $request->payment_id;
            $seller = $suborder->seller;
            $seller->seller_success_incoming += $suborder->transfer_price - ($suborder->sub_order_price - $suborder->refund_price);
            if (PaymentMethod::find($request->payment_id)->hlj_id == $seller->hlj_id) {
                $suborder->save();
                $seller->save();
                $notice = new WXNotice();
                $notice->notifySellerWithDrawSuccess($seller->user->openid, sprintf('%.2f', $suborder->transfer_price), date('Y-m-d H:i:s'));
//                return redirect(url('operator/waitTransfer/'.$request->page));
                return back();
            }
            abort('412');
        }
    }

    /**
     *
     * 买家管理
     * @return $this
     */
    public function buyerManagement()
    {
        $buyers = $this->buyer->getAvailableBuyersWithPaginate(20);
        return view('operation.buyerManagement')->with(['buyers' => $buyers]);
    }

    /**
     * 买家管理详情页
     * @param Request $request
     * @return $this
     */
    public function buyerManagementDetail(Request $request)
    {
        $buyer_id = $request->buyerId;
        $buyer = $this->buyer->getById($buyer_id);
        return view('operation.buyerManagementDetail')->with(['buyer' => $buyer]);
    }

    /**
     * 买家查询
     * @param Request $request
     * @return $this
     */
    public function searchBuyer(Request $request)
    {
        if (($request->registerTime2 == $request->registerTime) && ($request->registerTime != '')) {
            $request->registerTime2 = date('Y-m-d H:i:s', strtotime($request->registerTime2) + 24 * 60 * 60);
        }
        $duration = strtotime($request->registerTime2) - strtotime($request->registerTime);
        if (($request->weixin_id != '') || ($request->buyerEmail != '') || ($request->buyerMobile != '')) {
            if ($request->weixin_id != '') {
                $hlj_id = User::where('wx_number', 'like', '%' . $request->weixin_id . '%')->orWhere('mobile', $request->buyerMobile)->orWhere('email', $request->buyerEmail)->first();
                if (count($hlj_id) != 0) {
                    $buyers = Buyer::where('hlj_id', $hlj_id->hlj_id)->get();
                }
            } else {
                $hlj_id = User::Where('mobile', $request->buyerMobile)->orWhere('email', $request->buyerEmail)->first();
                if (count($hlj_id) != 0) {
                    $buyers = Buyer::where('hlj_id', $hlj_id->hlj_id)->get();
                }
            }
            if (count($hlj_id) == 0) {
                abort(431);
            }
        } else {
            if ($request->name != '') {
                $name = $request->name;
                if ($duration != 0) {

                    $buy_ers = DB::table('buyers')->join('users', function ($join) {
                        $join->on('buyers.hlj_id', '=', 'users.hlj_id');
                    })->
                    where('users.nickname', 'like', '%' . $name . '%')->whereBetween('created_at', [$request->registerTime, $request->registerTime2])->get();
                    $buyers = collect($buy_ers);
                } elseif ($duration == 0) {
                    $buy_ers = DB::table('buyers')->join('users', function ($join) {
                        $join->on('buyers.hlj_id', '=', 'users.hlj_id');
                    })->
                    where('users.nickname', 'like', '%' . $name . '%')->get();
                    $buyers = collect($buy_ers);
                }
            } else {
                if ($duration != 0) {
                    $buyers = Buyer::whereBetween('created_at', [$request->registerTime, $request->registerTime2])->get();
                } else {
                    $buyers = Buyer::all();
                }
            }
        }
        $buyersAll = $buyers->sortByDesc('buyer_actual_paid');
        $page = $request->page;
        $buyer = $buyersAll->forPage($page, 20);
        $buyers = new LengthAwarePaginator($buyer, count($buyersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.buyerSearched')->with(['buyers' => $buyers]);
    }

    /**
     * 打款查询
     * @param Request $request
     * @return $this
     */
    public function searchWithdraw(Request $request)
    {
        if ($request->order_id != '') {
            $suborders = SubOrder::where('sub_order_number', $request->order_id)->get();
        } else {
            if ($request->operator_id != 0) {
                if ($request->remittance_status != 0) {
                    $sub_orders = DB::table('sub_orders')->join('requirements', function ($join) {
                        $join->on('sub_orders.main_order_id', '=', 'requirements.main_order_id');
                    })->
                    where('requirements.operator_id', '=', $request->operator_id)->where('sub_orders.withdraw_state', '=', $request->remittance_status)->get();
                    $suborders = collect($sub_orders);
                } else {
                    $sub_orders = DB::table('sub_orders')->join('requirements', function ($join) {
                        $join->on('sub_orders.main_order_id', '=', 'requirements.main_order_id');
                    })->
                    where('requirements.operator_id', '=', $request->operator_id)->where('sub_orders.withdraw_state', '>', $request->remittance_status)->get();
                    $suborders = collect($sub_orders);
                }
            } else {
                if ($request->remittance_status != 0) {
                    $suborders = SubOrder::where('withdraw_state', $request->remittance_status)->get();
                } else {
                    $suborders = SubOrder::where('withdraw_state', '>', $request->remittance_status)->get();
                }
            }
        }
        $subordersAll = $suborders->sortByDesc('updated_at');
        $page = $request->page;
        $suborder = $subordersAll->forPage($page, 20);
        $subOrders = new LengthAwarePaginator($suborder, count($subordersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.withdrawSearched')->with(['subOrders' => $subOrders]);
    }

    /**
     * 订单查询
     * @param Request $request
     * @return $this
     */
    public function searchOrder(Request $request)
    {
        if (($request->orderTime2 == $request->orderTime) && ($request->orderTime != '')) {
            $request->orderTime2 = date('Y-m-d H:i:s', strtotime($request->orderTime2) + 24 * 60 * 60);
        }
        $duration = strtotime($request->orderTime2) - strtotime($request->orderTime);
        $operator_id = $request->operator_id;
        if ($request->order_id != '') {
            $suborders = SubOrder::where('sub_order_number', $request->order_id)->get();
        } else {
            if ($request->buyer_name != '') {
                $seller_id = [];
                $sellers = DB::table('sellers')->where('real_name', 'like', '%' . $request->buyer_name . '%')->get();
                if (count($sellers) != 0) {
                    foreach ($sellers as $seller) {
                        array_push($seller_id, $seller->seller_id);
                    }
                    $query = DB::table('sub_orders')->whereIn('seller_id', $seller_id);
                    $suborders = $query->get();
                    $state = 1;
                } else {
                    abort(431);
                }
            } else {
                if (($request->receiver_name != '') || ($request->receiver_mobile != '')) {
                    $receiver_id = [];
                    $receivers = DB::table('receiving_addresses')->where('receiver_name', $request->receiver_name)->orWhere('receiver_mobile', $request->receiver_mobile)->get();
                    if (count($receivers) != 0) {
                        foreach ($receivers as $receiver) {
                            array_push($receiver_id, $receiver->receiving_addresses_id);
                        }
                        $query = DB::table('sub_orders')->whereIn('receiving_addresses_id', $receiver_id);
                        $suborders = $query->get();
                        $state = 1;
                    } else {
                        abort(431);
                    }
                } else {
                    if (($request->buyerPhone != '') || ($request->buyerEmail != '')) {
                        $hlj_id = User::where('email', $request->buyerEmail)->orWhere('mobile', $request->buyerPhone)->first();
                        if (count($hlj_id) != 0) {
//                    $requirements = DB::table('requirements')->where('hlj_id',$hlj_id->hlj_id)->get();
                            $mainOrders = DB::table('main_orders')->where('hlj_id', $hlj_id->hlj_id)->get();
                            $main_id = [];
//                    foreach($requirements as $requirement)
//                    {
//                        array_push($main_id,$requirement->main_order_id);
//                    }
                            foreach ($mainOrders as $mainOrder) {
                                array_push($main_id, $mainOrder->main_order_id);
                            }
                            $query = DB::table('sub_orders')->whereIn('main_order_id', $main_id);
                            $suborders = $query->get();
                            $state = 1;
                        } else {
                            abort(431);
                        }
                    } else {
                        $state = 0;
                        $query = DB::table('sub_orders');
                    }
                }
            }
            if ($state == 1 || $state == 0) {
                if ($request->country_id != 0) {
                    $id = $request->country_id;
                    $query2 = $query->where('country_id', $id);
                    $suborders = $query2->get();
                } else {
                    $query2 = $query;
                }

                if ($request->order_states != 0) {
                    if ($request->order_states == 4) {
                        $query3 = $query2->where('sub_order_state', 411)->orWhere('sub_order_state', 431)->orWhere('sub_order_state', 441);
                    } elseif ($request->order_states == 5) {
                        $query3 = $query2->where('sub_order_state', 541)->orWhere('sub_order_state', 241);
                    } else {
                        $query3 = $query2->where('sub_order_state', $request->order_states);
                    }
                    $suborders = $query3->get();
                } else {
                    $query3 = $query2;
                    $suborders = $query3->get();
                }
                if ($duration != 0) {
                    $query4 = $query3->whereBetween('created_at', [$request->orderTime, $request->orderTime2]);
                    $suborders = $query4->get();
                } else {
                    $query4 = $query3;
                    $suborders = $query4->get();
                }
                if ($operator_id != 0) {
                    $query5 = $query4->where('operator_id', $operator_id);
                    $suborders = $query5->get();
                } else {
                    $query5 = $query4;
                    $suborders = $query5->get();
                }

            }
        }
        $suborders = collect($suborders);
        $suborders = $suborders->reject(function ($suborder) {
            return $suborder->sub_order_state == 101;
        });
        $subordersAll = $suborders->sortByDesc('updated_at');
        $page = $request->page;
        $suborder = $subordersAll->forPage($page, 20);
        $subOrders = new LengthAwarePaginator($suborder, count($subordersAll), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.orderSearched')->with(['subOrders' => $subOrders]);
    }

    public function exportExcel(Request $request)
    {
        $date = $request->date;
        $dateEnd = date('Y-m-d H:i:s', strtotime($date) + 24 * 60 * 60);
        $data = array();
        $data[0] = array(
            "订单号", "订单类型", "报价时间", "付款时间", "处理人", "国家", "买手",
            "商品名称", "件数", "商品总价", "邮费", "订单总价", "订单备注", "订单状态",
            "买家昵称", "买家电话", "买家邮箱",
            "收货人姓名", "收货人电话", "收货地址", "收货邮编",
            "物流公司1", "物流单号1", "物流公司2", "物流单号2",
            "退款类型", "退款金额", "退款备注", "实付金额", "实际打款金额",
            "实际买手", "国外物流公司", "国外物流单号",
            "转账商品总额", "转账邮费", "是否转账", "是否验收", "验收备注", "国内物流单号", "国内运费"
        );
        $subs = \App\Models\SubOrder::where('ppp_status', 1)->whereBetween('payment_time', [$date, $dateEnd])->get();
        for ($i = 0; $i < count($subs); $i++) {
            $sub = $subs[$i];
            $items = $sub->items;
            $memos = $sub->subOrderMemos;
            $title = '';
            $memo_text = '';
            $count = 0;


            $first_company = '';
            $first_number = '';
            $second_company = '';
            $second_number = '';
            $sub_type = '';
            if ($sub->order_type == 0) {
                $sub_type = '我要买';
            }
            if ($sub->order_type == 1) {
                $sub_type = '团购';
            }
            if ($sub->order_type == 2) {
                $sub_type = '小星星';
            }
            if ($sub->order_type == 3) {
                $sub_type = '福袋';
            }
            if ($sub->order_type == 4) {
                $sub_type = '秒杀商品';
            }

            // 第一段物流
            if ($sub->deliveryInfo) {
                if ($sub->deliveryInfo->deliveryCompany) {
                    $first_company = $sub->deliveryInfo->deliveryCompany->company_name;
                    $first_number = $sub->deliveryInfo->delivery_order_number;
                } else {
                    $first_company = $sub->deliveryInfo->delivery_company_info;
                    $first_number = $sub->deliveryInfo->delivery_order_number;
                }
            }

            // 导出二段物流
            if ($sub->is_second_phase && $sub->second_phase_info) {
                $second_info = json_decode($sub->second_phase_info);
                $second_company = \App\Models\DeliveryCompany::find($second_info['delivery_company_id'])->company_name;
                $second_number = $second_info['delivery_order_number'];
            }

            $refund_type = '';
            $refund_amount = 0;
            $refund_text = '';
            // 退款部分
            if ($sub->refunds) {
                foreach ($sub->refunds as $refund) {
                    if ($refund->refund_type == 1) {
                        $refund_type .= '退部分款' . ";\n";
                    } elseif ($refund->refund_type == 2) {
                        $refund_type .= '退全款' . ";\n";
                    }
                    $refund_amount += $refund->refund_price;
                    $refund_text .= $refund->description . ";\n";
                }

            }

            // 需要打款金额
            $actual_pay = 0;
            if ($sub->transfer_price) {
                $actual_pay = $sub->transfer_price;
            }

            foreach ($items as $item) {
                if ($item->is_positive) {
                    $title .= $item->title . '(' . $item->skus[0]->sku_inventory . ')' . ";\n";
                    $count += $item->skus[0]->sku_inventory;
                } else {
                    $title .= $item->title . '(' . $sub->groupItems->first()->number . ')' . ";\n";
                }
            }

            if ($sub->order_type == 1) {
                $count = $sub->groupItems->first()->number;
            }

            foreach ($memos as $memo) {
                $memo_text .= $memo->content . ";\n";
            }
            $province_code = $sub->receivingAddress->first_class_area;
            $city_code = $sub->receivingAddress->second_class_area;
            $county_code = $sub->receivingAddress->third_class_area;
            $street_address = $sub->receivingAddress->street_address;
            $province_level = $this->regionInstance->getRegionByCode($province_code)->name;
            if ($city_code == 1) {
                $city_level = "";
            } else {
                $city_level = $this->regionInstance->getRegionByCode($city_code)->name;
            }
            if ($county_code == 1) {
                $county_level = "";
            } else {
                $county_level = $this->regionInstance->getRegionByCode($county_code)->name;
            }
            rtrim($title, "\n");
            rtrim($title, ";");
            rtrim($memo_text, "\n");
            rtrim($memo_text, ";");
            rtrim($refund_type, "\n");
            rtrim($refund_type, ";");
            rtrim($refund_text, "\n");
            rtrim($refund_text, ";");
            $data[$i + 1] = array(
                $sub->sub_order_number,
                $sub_type,
                $sub->created_offer_time,
                $sub->payment_time,
                $sub->operator->real_name . '     ',
                $sub->country->name . '     ',
                $sub->seller->real_name . '     ',
                $title,
                $count . '     ',
                $sub->sub_order_price - $sub->postage,
                $sub->postage,
                $sub->sub_order_price,
                $memo_text,
                $sub->sub_order_state . '     ',
                $sub->mainOrder->user->nickname . '    ',
                $sub->mainOrder->user->mobile,
                $sub->mainOrder->user->email,
                $sub->receivingAddress->receiver_name . '    ',
                $sub->receivingAddress->receiver_mobile,
                $province_level . $city_level . $county_level . $street_address,
                $sub->receivingAddress->receiver_zip_code,
                $first_company,
                $first_number,
                $second_company,
                $second_number,
                $refund_type,
                $refund_amount,
                $refund_text,
                $sub->sub_order_price - $refund_amount,
                $actual_pay
            );
        }
        Excel::create(date('Y-m-d', strtotime($date)) . '订单', function ($excel) use ($data) {
            $excel->sheet('当日付款订单', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true, false);
            });
        })->download('xls');
        return redirect(url('operator/waitPay/?page=1'));
    }

    public function getBuyerInfo(Request $request)
    {
        $from = $request->from;
        $to = $request->to ? $request->to : User::all()->count();
        $data = array();
        $data[0] = array(
            "买家昵称", "买家电话", "买家邮箱",
            "省", "市",
            "授权时间", "最近活动时间", "成为买家时间", "需求次数",
            "付款次数", "付款总额"
        );
        $users = User::where('hlj_id', '>=', $from)->where('hlj_id', '<=', $to)->get();
        for ($i = 0; $i < count($users); $i++) {
            $currentUser = $users[$i];
            $currentBuyer = $currentUser->buyer;
            $data[$i + 1] = array(
                $this->userTextEncode($currentUser->nickname),
                $currentUser->mobile,
                $currentUser->email,
                $currentUser->province,
                $currentUser->city,
                $currentUser->created_at,
                $currentUser->updated_at,

                $currentBuyer ? $currentBuyer->created_at : ' ',
                $currentBuyer ? $currentBuyer->buyer_requirements_num : ' ',
                $currentBuyer ? $currentBuyer->buyer_paid_count : ' ',
                $currentBuyer ? $currentBuyer->buyer_initial_paid : ' '
            );
        }

        Excel::create(date('Y-m-d') . '买家汇总to'. $to, function ($excel) use ($data) {
            $excel->sheet('买家总表', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true, false);
            });
        })->download('xls');
    }


    public function getKillInfo()
    {
        $ids = Cache::get('SecKill:Users');
        $clicks = Cache::get('SecKill:CanClick');
        $cannotClicks = Cache::get('SecKill:CannotClick');
        $remind = Cache::get('SecKill:Remind');
        $users = [];
        foreach($ids as $id) {
            array_push($users, User::find($id));
        }

        $data = array();
        $data[0] = array(
            "买家昵称", "买家电话", "买家邮箱",
            "省", "市",
            "授权时间", "最近活动时间", "成为买家时间", "需求次数",
            "付款次数", "付款总额", "秒杀次数", "是否设置提醒", "抢光仍然点击次数"
        );
        for ($i = 0; $i < count($users); $i++) {
            $currentUser = $users[$i];
            $currentBuyer = $currentUser->buyer;

            $data[$i + 1] = array(
                $this->userTextDecode($currentUser->nickname),
                $currentUser->mobile,
                $currentUser->email,
                $currentUser->province,
                $currentUser->city,
                $currentUser->created_at,
                $currentUser->updated_at,

                $currentBuyer ? $currentBuyer->created_at : ' ',
                $currentBuyer ? $currentBuyer->buyer_requirements_num : ' ',
                $currentBuyer ? $currentBuyer->buyer_paid_count : ' ',
                $currentBuyer ? $currentBuyer->buyer_initial_paid : ' ',
                $clicks[$users[$i]->hlj_id],
                in_array($users[$i]->hlj_id, $remind) ? 'Yes': 'No',
                isset($cannotClicks[$users[$i]->hlj_id]) ? $cannotClicks[$users[$i]->hlj_id] : 0,
            );
        }

        Excel::create(date('Y-m-d') . '抢购测试', function ($excel) use ($data) {
            $excel->sheet('抢购测试', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true, false);
            });
        })->download('xls');
    }

    public function addHistorySnapshot()
    {
        $subs = SubOrder::where('charge_id', '!=', '')->get();
        foreach($subs as $sub) {
            $this->suborder->createOrUpdateSubOrderPaidSnapshot($sub);
        }
    }

    public function addHistoryLucky()
    {
        $subs = SubOrder::where('order_type', 2)->get();
        foreach($subs as $sub) {
            $this->suborder->createOrUpdateSubOrderPaidSnapshot($sub);
        }
    }
    
    public function userTextEncode($str)
    {
        if (!is_string($str)) return $str;
        if (!$str || $str == 'undefined') return '';

        $text = json_encode($str);
        $text = preg_replace(<<<EMOJI
/(\\\u[ed][0-9a-f]{3})/i
EMOJI
            , "", $text);
        return json_decode($text);
    }

    public function tagsManagement()
    {
        $tags = ItemTag::where('hide', 0)->with('operator')->get();
        $filtered = [];
        foreach($tags as $tag) {
            array_push($filtered, [
                'id' => $tag->item_tag_id,
                'tag_name' => $tag->tag_name,
                'tag_description' => $tag->tag_description,
                'style' => json_decode($tag->tag_attributes)->style,
                'priority' => $tag->priority,
                'is_available' => $tag->is_available,
                'operator_name' => $tag->operator->real_name,
            ]);
        }

        return view('operation.itemsManagement.operatingTags')->with(['tags' => $filtered]);
    }

    public function removeUsersMessage($openid)
    {
        $wildDog = new WilddogLib('https://buypal.wilddogio.com/', 'tjXo3cQBnYkULU71ngriY5wKlq2QcUsVSRE0Qxnh');
        $wildDog->delete('' . $openid);
    }
}
