<div class="query panel-body" style="background-color: #f0f0f0">
    <form class="form-horizontal" role="form" method="get" action="{{url('operator/searchWithdraw')}}">
       
        <div class="col-lg-4 col-md-4">
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

            <label for="remittance_operator_id" class="col-lg-4 col-md-4 control-label">打款人</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="remittance_operator_id" id="remittance_operator_id">
                    <option value="0">李文娟</option>
                </select>
            </div>
        </div>

        <div class="col-lg-4">
            <label for="remittance_status" class="col-lg-4 col-md-4 control-label">打款状态</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="remittance_status" id="remittance_status">
                    <option value="0">全部</option>
                    <option value="1">待确认</option>
                    <option value="2">待打款</option>
                    <option value="3">已打款</option>
                </select>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-4">
            <label for="order_id" class="col-lg-4 col-md-4 control-label">订单ID</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="order_id" id="order_id">
            </div>
        </div>
        <input class="hidden" name="page" value="1">
        <div class="col-lg-12 col-md-12" style="text-align: center">
            <button type="submit" class="btn btn-default">查询</button>
        </div>

    </form>
</div>