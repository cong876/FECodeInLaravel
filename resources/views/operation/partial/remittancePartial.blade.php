<div class="remittanceHeader panel-body">
    <ul class="nav nav-pills">
        <li id="remittanceAll">
            <a href="{{url('operator/allCapital/?page=1')}}">全部</a>
        </li>
        <li id="pending">
            <?php
            $sub_orders = App\Models\SubOrder::where('withdraw_state',1)->get();
            $transfer_orders = App\Models\SubOrder::where('withdraw_state',2)->get();
            $subOrders = $sub_orders->filter(function($suborder){
                if(($suborder->operator_id == Auth::user()->employee->employee_id))
                {
                    return  $suborder;
                }
            });
            $transferOrders = $transfer_orders->filter(function($suborder){
                if(($suborder->operator_id == Auth::user()->employee->employee_id)||($suborder->operator->op_level>3))
                {
                    return  $suborder;
                }
            });
                $count = count($subOrders);
                $count_transfer = count($transfer_orders);
            ?>
            <a href="{{url('operator/waitEnsureCapital/?page=1')}}">待确认<span class="badge" style="background-color:blueviolet;">{{$count}}</span></a>
        </li>
        <li id="waitRemittance">
            <a href="{{url('operator/waitTransfer/?page=1')}}">待打款<span class="badge" style="background-color:blueviolet;">{{$count_transfer}}</span></a>
        </li>
        <li id="remittanced">
            <a href="{{url('operator/hasTransferred/?page=1')}}">已打款</a>
        </li>
    </ul>
</div>