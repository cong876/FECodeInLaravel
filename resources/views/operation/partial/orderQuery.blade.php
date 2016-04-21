<div class="query panel-body" style="background-color: #f0f0f0">
    <form class="form-horizontal" role="form" method="get" action="{{url('operator/searchOrder')}}">
        <div class="col-lg-4 col-md-4">

            <label for="order_id" class="col-lg-4 col-md-4 control-label">订单号</label>
            <div class="form-group input-group-sm col-md-8 col-lg-8">
                <input type="text" class="form-control" name="order_id" id="order_id">
            </div>

            <label for="order_states" class="col-lg-4 col-md-4 control-label">订单状态</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="order_states" id="order_states">
                    <option value="0">全部</option>
                    <option value="201">待付款</option>
                    <option value="501">待发货</option>
                    <option value="521">审核中</option>
                    <option value="601">已发货</option>
                    <option value="301">已完成</option>
                    <option value="4">交易关闭</option>
                    <option value="5">拒单待分配</option>
                </select>
            </div>

            <label for="orderTime" class="col-lg-4 col-md-4 control-label">下单时间</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="date" class="form-control" name="orderTime" id="orderTime">
            </div>

            <label for="orderTime2" class="col-lg-4 col-md-4 control-label">至</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="date" class="form-control" name="orderTime2" id="orderTime2">
            </div>

        </div>
        <div class="col-lg-4">

            <label for="buyerPhone" class="col-lg-4 col-md-4 control-label">买家注册电话</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="buyerPhone" id="buyerPhone">
            </div>

            <label for="buyerEmail" class="col-lg-4 col-md-4 control-label">买家邮箱</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="buyerEmail" id="buyerEmail">
            </div>

            <label for="receiver_name" class="col-lg-4 col-md-4 control-label">收货人姓名</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="receiver_name" id="receiver_name">
            </div>

            <label for="receiver_mobile" class="col-lg-4 col-md-4 control-label">收货人电话</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="receiver_mobile" id="receiver_mobile">
            </div>                        

        </div>
        <div class="col-lg-4 col-md-4">

            <label for="country_id" class="col-lg-4 col-md-4 control-label">分配国家</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="country_id" id="country_id">
                    <option value="0">全部</option>
                    <<?php $countries = App\Models\Country::get(); ?>
                    @foreach($countries as $country)
                        <option value="{{$country->country_id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </div>        
            
            <label for="buyer_name" class="col-lg-4 col-md-4 control-label">买手姓名</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="buyer_name" id="buyer_name">
            </div>
            
            {{--<label for="buyer_id" class="col-lg-4 col-md-4 control-label">买手id</label>--}}
            {{--<div class="form-group input-group-sm col-lg-8 col-md-8">--}}
                {{--<input type="text" class="form-control" name="buyer_id" id="buyer_id">--}}
            {{--</div> --}}

            <label for="operator_id" class="col-lg-4 col-md-4 control-label">处理人</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="operator_id" id="operator_id">
                    <option value="0">全部</option>
                    <?php $employees = App\Models\Employee::get();?>
                    @foreach($employees as $employee)
                    <option value="{{$employee->employee_id}}">{{$employee->real_name}}</option>
                    @endforeach
                </select>
            </div>
            <input class="hidden" name="page" value="1">
        </div>
        <div class="col-lg-12 col-md-12" style="text-align: center">
            <button type="submit" class="btn btn-default">查询</button>
        </div>
    </form>
</div>