<div class="detailHeader panel-body">
    <ul class="nav nav-pills">
        <li id="requirementAll">
            <a href="{{url('operator/getAllRequirement/?page=1')}}">全部</a>
        </li>
        <li id="requirementWaitAccept">
            <?php
            $requirement = App\Models\Requirement::where('state',101)->where('operator_id',0)->get();
            $count = count($requirement);
            ?>
            <a href="{{url('operator/waitAccept/?page=1')}}">需求待领取<span class="badge" style="background-color:blueviolet;">{{$count}}</span></a>
        </li>
        <li id="requirementWaitGenerateItems">
            <a href="{{url('operator/waitResponse/?page=1')}}">待生成商品</a>
        </li>
        <li id="requirementWaitSplitOrder">
            <a href="{{url('operator/waitSplit/?page=1')}}">待分配订单</a>
        </li>
        {{--<li id="requirementWaitSendPrice">--}}
            {{--<a href="{{url('operator/waitSendPrice')}}">待发送报价</a>--}}
        {{--</li>--}}
        {{--<li id="requirementFinished">--}}
            {{--<a href="{{url('operator/showFinishedRequirement')}}">需求已完成</a>--}}
        {{--</li>--}}
        <li id="requirementClosed">
            <a href="{{url('operator/showClosedRequirement/?page=1')}}">需求已关闭</a>
        </li>
    </ul>
</div>