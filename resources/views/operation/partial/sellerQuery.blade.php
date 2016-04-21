<div class="query panel-body" style="background-color: #f0f0f0">
    <form class="form-horizontal" role="form" method="get" action="{{url('operator/searchSeller')}}">
        <div class="col-lg-4 col-md-4">

            <label for="country_id" class="col-lg-4 col-md-4 control-label">国家</label>
            <div class="form-group input-group-sm col-md-8 col-lg-8">
                <select class="form-control" name="country_id" id="country_id">
                    <option value="0">全部</option>
                    <?php $countries = App\Models\Country::get(); ?>
                    @foreach($countries as $country)
                        <option value="{{$country->country_id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </div>

            <label for="realname" class="col-lg-4 col-md-4 control-label">真实姓名</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="realname" id="realname">
            </div>
            
            <label for="weixin_id" class="col-lg-4 col-md-4 control-label">微信号</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="weixin_id" id="weixin_id">
            </div>

        </div>
        <div class="col-lg-4">

            <label for="sellerMobile" class="col-lg-4 col-md-4 control-label">手机号</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="sellerMobile" id="sellerMobile">
            </div>

            <label for="sellerEmail" class="col-lg-4 col-md-4 control-label">邮箱</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="text" class="form-control" name="sellerEmail" id="sellerEmail">
            </div>                   

        </div>
        <div class="col-lg-4 col-md-4">

            <label for="sellerStates" class="col-lg-4 col-md-4 control-label">买手状态</label>
            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <select class="form-control" name="sellerStates" id="sellerStates">
                    <option value="0">全部</option>
                    <option value="1">正常</option>
                    <option value="2">小黑屋</option>
                </select>
            </div>

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