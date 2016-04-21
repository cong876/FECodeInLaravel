<div class="detailHeader panel-body">
    <ul class="nav nav-pills">
        <li id="orderAll">
            <a href="{{url('/operator/getAllOrders/?page=1')}}">全部</a>
        </li>
        <li id="orderWaitPay">
            <a href="{{url('/operator/waitPay/?page=1')}}">待付款</a>
        </li>
        <li id="orderWaitSend">
            <a href="{{url('/operator/waitDelivery/?page=1')}}">待发货</a>
        </li>
        <li id="orderWaitSendGTSeven">
            <?php
            $sevenDaysSecond = date('Y-m-d H:i:s', time() - 7 * 24 * 3600);
            $count = App\Models\SubOrder::where('sub_order_state', 501)
                    ->where('payment_time', '<=', $sevenDaysSecond)->get()
                    ->sortByDesc('updated_at')->count();
            ?>
            <a href="{{url('/operator/waitDeliveryGTSeven/?page=1')}}">7天未发货<span class="badge" style="background-color:blueviolet;">{{$count}}</span></a>
        </li>
        <li id="orderAuditing">
            <?php
            $count_auditing = count(App\Models\SubOrder::where('sub_order_state',521)->get());
            ?>
            <a href="{{url('/operator/isAuditing/?page=1')}}">审核中<span class="badge" style="background-color:blueviolet;">{{$count_auditing}}</span></a>
        </li>
        <li id="orderSecondary">
            <a href="{{url('/operator/hasSecondaryDelivered/?page=1')}}">二段物流</a>
        </li>
        <li id="orderSent">
            <a href="{{url('/operator/hasDelivered/?page=1')}}">已发货</a>
        </li>
        <li id="orderCompleted">
            <a href="{{url('/operator/hasFinished/?page=1')}}">已完成</a>
        </li>
        <li id="orderClosed">
            <a href="{{url('/operator/orderClosed/?page=1')}}">交易已关闭</a>
        </li>
        <li id="orderSellerAssign">
            <?php
            $user = Auth::user();
                if($user->employee->op_level>3)
                    {
                        $count = count(App\Models\SubOrder::where('sub_order_state',241)->orWhere('sub_order_state',541)->get());
                    }
                else{
                    $sub_orders = App\Models\SubOrder::where('sub_order_state',241)->orWhere('sub_order_state',541)->get()->filter(function ($sub_order) {
                        if($sub_order->mainOrder->requirement->operator_id == Auth::user()->employee->employee_id) {
                            return $sub_order;
                        }
                    });
                    $count = count($sub_orders);
                }
            ?>
            <a href="{{url('/operator/orderSellerAssign/?page=1')}}">拒单待分配<span class="badge" style="background-color:blueviolet;">{{$count}}</span></a>
        </li>
        <li style="float: right">
            <form class="form-horizontal" role="form" method="get" action="{{url('/operator/exportExcel')}}">
                <div class="input-group" style="width: 200px">
                <input type="date" class="form-control" name="date" required="required" style="display: inline-block;width: 160px;">
                <span class="input-group-addon" style="padding: 0"><button type="submit" style="border: none;background: rgba(255,255,255,0);width: 100%;height: 100%">导出报表</button></span>
                </div>
            </form>
        </li>
    </ul>
</div>