<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;
use App\Repositories\DeliveryInfo\DeliveryInfoRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Seller\SellerRepositoryInterface;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\SubOrder;
use App\Models\BankCardBin;
use App\Helper\LogTest;
use App\Helper\WXNotice;
use App\Helper\LeanCloud;
use Hash;


class SellerController extends Controller
{
    private $paymentMethod, $suborder, $deliveryInfo, $user, $seller;

    function __construct(PaymentMethodRepositoryInterface $paymentMethod, DeliveryInfoRepositoryInterface $deliveryInfo,
                         SubOrderRepositoryInterface $suborder, UserRepositoryInterface $user, SellerRepositoryInterface $seller)
    {
        $this->paymentMethod = $paymentMethod;
        $this->deliveryInfo = $deliveryInfo;
        $this->suborder = $suborder;
        $this->user = $user;
        $this->seller = $seller;
        // 注入依赖中间件
        $this->middleware('wechatauth');
        $this->middleware('seller');

    }

    public function index()
    {
        return view('sellerOrder');
    }

    /*
     *
     * 添加新的支付方式
     * 返回值-1 表示支付信息与数据库重复，0表示安全密码错误，-2表示银行卡信息错误
     */
    public function createPayment(Request $request)
    {
        $user = Auth::user();
        if(($request->channel == 1) && (!BankCardBin::getCardInfo($request->identification)))
        {
            return response()->json(-2);
        }
        else
        {
            if (Hash::check($request->password, $user->secure_password))
            {
                $payment = $this->paymentMethod->createPaymentMethod(array('account_name' => $request->name), $user->hlj_id,
                    $request->channel, $request->identification);
                if(!$payment)
                {
                    return response()->json(-1);
                }
                $this->paymentMethod->setOrUpdateDefaultPaymentMethod($payment, $user->hlj_id);
                if ($payment->channel == 1) {
                    $response = ['payment_id' => $payment['payment_methods_id'], 'identification' => $payment['identification'], 'back_info' => $payment->bankInfo];
                } else {
                    $response = ['payment_id' => $payment['payment_methods_id'], 'identification' => $payment['identification']];
                }
                return response()->json($response);
            }
            else {
                return response()->json(0);
            }
        }
    }

    /*
     *
     * 更新支付方式
     * 返回值-1 表示支付信息与数据库重复，0表示安全密码错误，-2表示银行卡信息错误
     */
    public function updatePayment(Request $request)
    {
        $user = Auth::user();
        if(($request->channel == 1) && (!BankCardBin::getCardInfo($request->identification)))
        {
            return response()->json(-2);
        }
        else
        {
            if (Hash::check($request->password, $user->secure_password))
            {
                $payment = $this->paymentMethod->getById($request->payment_id);
                $result = $this->paymentMethod->updatePaymentMethod($payment, array('account_name' => $request->name), $request->channel, $request->identification);
                if(!$result)
                {
                    return response()->json(-1);
                }
                if ($payment->channel == 1)
                {
                    $response = ['payment_id' => $payment['payment_methods_id'], 'identification' => $payment['identification'], 'back_info' => $payment->bankInfo];
                }
                else
                {
                    $response = ['payment_id' => $payment['payment_methods_id'], 'identification' => $payment['identification']];
                }
                return response()->json($response);
            }
            else
            {
                return response()->json(0);
            }
        }
    }

    /*
     *
     * 将支付方式置为默认
     *
     */
    public function setToDefault(Request $request)
    {
        $payment = $this->paymentMethod->getById($request->payment_id);
        $this->paymentMethod->setOrUpdateDefaultPaymentMethod($payment, $payment->hlj_id);
        return response()->json(1);
    }

    /*
     *
     * 删除该支付方式
     *
     */
    public function deletePayment(Request $request)
    {
        $payment = $this->paymentMethod->getById($request->payment_id);
        $hlj_id = $payment->hlj_id;
        if (count(PaymentMethod::where('hlj_id', $hlj_id)->get()) == 2) {
            $this->paymentMethod->deletePaymentMethod($payment);
            $paymentMethod = PaymentMethod::where('hlj_id', $hlj_id)->first();
            $paymentMethod->is_default = true;
            $paymentMethod->save();

        } else {
            $this->paymentMethod->deletePaymentMethod($payment);
        }
        return response()->json(1);
    }

    /*
     *
     * 买手已接单TAB
     *
     */
    public function ToggleToReceived()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->get();
        return view('sellerOrder.received')->with('subOrders', $subOrders);
    }

    /*
     *
     * 买手待发货TAB
     *
     */
    public function ToggleToNeedToDelivery()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->get();
        return view('sellerOrder.needToDelivery')->with('subOrders', $subOrders);
    }

    /*
     *
     * 买手待审核TAB
     *
     */
    public function ToggleToAuditing()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->get();
        return view('sellerOrder.auditing')->with('subOrders', $subOrders);
    }

    /*
     *
     * 买手未入账总额页各订单
     *
     */
    public function ToggleToWaitRevenue()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->get();
        return view('sellerOrder.waitRevenue')->with('subOrders', $subOrders);
    }

    /**
     *
     * 买手我的累计收入页可提现订单
     * @return $this
     */
    public function ToggleToRevenue()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = User::find($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->where('withdraw_state', 0)->whereIn('sub_order_state', [301, 601])
            ->where('transfer_price', '>', 0)->get()->sortByDesc('updated_at');
        return view('sellerOrder.revenue')->with('subOrders', $subOrders);
    }

    /**
     *
     * 买手我的累计收入页提现记录
     * @return $this
     */
    public function ToggleToWithdraw()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->where('withdraw_state', '>', 0)->get()->sortByDesc('updated_at');
        return view('sellerOrder.withdraw')->with(['subOrders'=>$subOrders,'hlj_id'=>$hlj_id]);
    }


    /*
     *
     * 买手已发货TAB
     *
     */
    public function ToggleToHasDelivered()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->get();
        return view('sellerOrder.hasDelivered')->with('subOrders', $subOrders);
    }

    /*
     *
     * 买手已完成TAB
     *
     */
    public function ToggleToHasFinished()
    {
        $hlj_id = Auth::user()->hlj_id;
        $seller_id = $this->user->getById($hlj_id)->seller->seller_id;
        $subOrders = SubOrder::where('seller_id', $seller_id)->where('transfer_price', '>', 0)->get();
        return view('sellerOrder.hasFinished')->with('subOrders', $subOrders);
    }

    /*
     *
     * 买手填写物流信息
     *
     */
    public function createDeliveryInfo(Request $request)
    {
        $sub_order_id = SubOrder::where('sub_order_number', $request->subOrderNumber)->first()->sub_order_id;
        if ($request->logistics_pinyin == "") {
            $url = "http://www.kuaidi100.com/";
            $deliveryInfo = $this->deliveryInfo->createDelivery(array('sub_order_id' => $sub_order_id,
                'delivery_order_number' => $request->logistics_number, 'delivery_company_id' => $request->logistics_id,
                'delivery_company_info' => $request->logistics_name, 'delivery_related_url' => $url));
        } else {
            $com = $request->logistics_pinyin;
            $url = $url = "http://m.kuaidi100.com/index_all.html?type=" . $com . "&postid=" . $request->logistics_number;
            $deliveryInfo = $this->deliveryInfo->createDelivery(array('sub_order_id' => $sub_order_id,
                'delivery_order_number' => $request->logistics_number, 'delivery_company_id' => $request->logistics_id,
                'delivery_company_info' => $request->logistics_name, 'delivery_related_url' => $url));
        }
        $id = $deliveryInfo->delivery_info_id;
        $suborder = $this->suborder->getById($sub_order_id);
        $suborder->sub_order_state = 521;
        $suborder->delivery_info_id = $id;
        $suborder->delivery_time = date('Y-m-d H:i:s');
        LogTest::writeSellerLog(Auth::user()->seller->real_name . '填写了物流信息', ['子订单号' => $suborder->sub_order_number]);
        $suborder->save();
        $items = $suborder->items;
        $title = '';
        foreach($items as $item)
        {
            $title .= $item->title . '；';
        }
        $title = rtrim($title,'；');
        if(mb_strlen($title)>8)
        {
            $title_send = mb_substr($title,0,8). '...';
        }
        else
        {
            $title_send = $title.'。';
        }
        if(mb_strlen($title)>12)
        {
            $title_notice = mb_substr($title,0,12) . '...';
        }
        else{
            $title_notice = $title;
        }
        $hlj_id = $suborder->mainOrder->hlj_id;
        $mobile = User::find($hlj_id)->mobile;
        $buyer_openid = User::find($hlj_id)->openid;
        $notice = new WXNotice();
        $notice->deliverItems($buyer_openid,$suborder->sub_order_number,
            $title_notice,$suborder->sub_order_price - $suborder->refund_price, $suborder->sub_order_id);
        LeanCloud::sellerDeliverItemsSMS($mobile,$title_send,$suborder->operator->user->mobile);
        return response()->json(1);
    }

    /*
     *
     * 买手拒单
     *
     */
    public function cancelSeller(Request $request)
    {
        $hlj_id = Auth::user()->hlj_id;
        $user = $this->user->getById($hlj_id);
        $subOrderId = SubOrder::where('sub_order_number', $request->subOrderNumber)->first()->sub_order_id;
        $suborder = $this->suborder->getById($subOrderId);
//        $this->suborder->updateSubOrder($suborder, array('country_id' => "", 'seller_id' => ""));
        if ($suborder->sub_order_state == 201) {
            $suborder->sub_order_state = 241;
            $suborder->save();
            $user->seller->seller_refuse_orders_num += 1;
            $user->save();
        }
        if ($suborder->sub_order_state == 501) {
            $suborder->sub_order_state = 541;
            $suborder->save();
            $user->seller->seller_refuse_orders_num += 1;
            $user->save();
        }
        LogTest::writeSellerLog(Auth::user()->seller->real_name . '拒绝了接单', ['订单号' => $request->subOrderNumber]);
        return response()->json(1);
    }

    /**
     *
     * 申请提现
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyWithdraw(Request $request)
    {
        $sub_number = $request->subOrderNumber;
        $sub_id = SubOrder::where('sub_order_number', $sub_number)->first()->sub_order_id;
        $suborder = $this->suborder->getById($sub_id);
        $suborder->withdraw_state = 1;
        $suborder->save();
         $notice = new WXNotice();
         $notice->notifySellerWithDrawRequest(Auth::user()->openid, sprintf('%.2f', $suborder->transfer_price), date('Y-m-d H:i:s'));
        return response()->json(1);
    }

    /**
     *
     * 买手重置安全密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $secure_password = bcrypt($request->password);
        $user = Auth::user();
        $this->user->updateInfo($user,array('secure_password' => $secure_password));
        return response()->json(1);
    }

    /**
     * 验证该手机是不是买手本人注册手机
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyMobileNumber(Request $request)
    {
        $mobile = $request->mobile;
        $user = Auth::user();
        if($user->mobile == $mobile)
        {
            return response()->json(1);
        }
        else
        {
            return response()->json(0);
        }
    }

    /**
     * 拉起重置密码页
     * @return \Illuminate\View\View
     */
    public function getResetPage()
    {
        return view('resetPassword');
    }

}