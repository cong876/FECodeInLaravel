<div class="query panel-body" style="background-color: #f0f0f0">
    <form class="form-horizontal" role="form" method="get" action="{{url('operator/searchBuyer')}}">
        <div class="col-lg-4 col-md-4">

            <label for="name" class="col-lg-4 col-md-4 control-label">昵称</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="name" id="name">
            </div>

            <label for="weixin_id" class="col-lg-4 col-md-4 control-label">微信号</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="weixin_id" id="weixin_id">
            </div>

        </div>
        <div class="col-lg-4">

            <label for="buyerMobile" class="col-lg-4 col-md-4 control-label">手机号</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="buyerMobile" id="buyerMobile">
            </div>

            <label for="buyerEmail" class="col-lg-4 col-md-4 control-label">邮箱</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="buyerEmail" id="buyerEmail">
            </div>

        </div>
        <div class="col-lg-4 col-md-4">

            <label for="registerTime" class="col-lg-4 col-md-4 control-label">注册时间</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="date" class="form-control" name="registerTime" id="registerTime">
            </div>

            <label for="registerTime2" class="col-lg-4 col-md-4 control-label">至</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="date" class="form-control" name="registerTime2" id="registerTime2">
            </div>
            <input class="hidden" name="page" value="1">
        </div>
        <div class="col-lg-12 col-md-12" style="text-align: center">
            <button type="submit" class="btn btn-default">查询</button>
        </div>
    </form>
</div>