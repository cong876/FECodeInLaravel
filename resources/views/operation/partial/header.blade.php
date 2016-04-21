<div class="nav navbar navbar-inverse navbar-fixed-top">
    <ul id="Tab" class="nav navbar-nav">
        <li class="navbar-header" id="yeyeHead">
            <a class="navbar-brand" href="#" id="surprise">
                @YEYE^_^
            </a>
        </li>
        <li id="requirementMangement">
            <a href="{{url('/operator/waitAccept/?page=1')}}">需求管理</a>
        </li>
        <li id="orderMangement">
            <a href="{{url('/operator/waitPay/?page=1')}}">订单管理</a>
        </li>
        <li id="activitiesManagement" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">活动管理<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="{{url('/operator/allActivities/?page=1')}}">日常活动</a></li>
                <li><a href="{{url('/operator/getGoldActivity')}}">金币活动</a></li>
                <li><a href="{{url('/operator/getLuckyBagActivity')}}">福袋活动</a></li>
            </ul>
        </li>
        <li id="itemsManagement" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">商品管理<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="{{url('/operator/tagsManagement')}}">标签管理</a></li>
                <li><a href="{{url('/infoManagement')}}">资讯库</a></li>
            </ul>
        </li>
        <li id="sellerManagement">
            <a href="{{url('/operator/getSeller/?page=1')}}">买手管理</a>
        </li>
        <li id="buyerManagement">
            <a href="{{url('/operator/buyerManagement/?page=1')}}">买家管理</a>
        </li>
<!--         <li>
            <a href="#">营销平台</a>
        </li> -->
        <li id="fundsManagement" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">资金管理<b class="caret"></b></a>
            <ul class="dropdown-menu">
               <li id="remittanceManagement"><a href="{{url('operator/waitEnsureCapital/?page=1')}}">打款管理</a></li>
               <li id="refundManagement"><a href="#">退款管理</a></li>
            </ul>
        </li>
        <li id="staffManagement">
            <a href="{{url('employee/getAllEmployee/?page=1')}}">员工管理</a>
        </li>
    </ul>
    <ul class="nav navbar-nav navbar-right hidden-sm" id="operator">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><div id="clock" style="color: greenyellow; font-size: 20px;"></div></a>
            <ul class="dropdown-menu" style="min-width: 108px; width: 108px;text-align: center">
                <li><a href="#">美国: <span id="clock2"></span></a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">英国: <span id="clock3"></span></a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">日本: <span id="clock5"></span></a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">澳洲: <span id="clock4"></span></a></li>
            </ul>
        </li>
        <li><a href="#">{{ Auth::check() ? Auth::user()->email : ''}}</a></li>
        <li><a href="{{url('operator/logout')}}">登出</a></li>
    </ul>
</div>
