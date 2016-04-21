<div class="query panel-body" style="background-color: #f0f0f0">
    <form class="form-horizontal" role="form" method="get" action="{{url('operator/searchRequirement')}}">
        <div class="col-lg-4 col-md-4">

            <label for="requirement_id" class="col-lg-4 col-md-4 control-label">需求号</label>
            <div class="form-group input-group-sm col-md-8 col-lg-8">
                <input type="text" class="form-control" name="requirement_id" id="requirement_id">
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

        </div>
        <div class="col-lg-4 col-md-4">

            <label for="country_id" class="col-lg-4 col-md-4 control-label">国家</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="country_id" id="country_id">
                    <option value="0">全部</option>
                    <?php $countries = App\Models\Country::get(); ?>
                    @foreach($countries as $country)
                        <option value="{{$country->country_id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </div>

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