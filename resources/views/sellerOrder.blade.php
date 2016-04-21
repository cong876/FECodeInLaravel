<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta content="email=no" name="format-detection"/>
    <title>买手中心</title>
    <link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon"/>
    <link rel="stylesheet" type="text/css" href={{url("css/sellerOrder.min.css")}}>
    <div id='wx_pic' style='margin:0 auto;display:none;'>
        <img src={{url("image/LOGO.jpg")}} />
    </div>
</head>
<body>

<?php
$hlj_id = Auth::user()->hlj_id;
$user = App\Models\User::find($hlj_id)    //有登陆认证页面后渲染登陆用户
?>

        <!-- loading页	 -->
<div id="loading">
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
    </div>
</div>

<!-- 主页 -->
	<div id="home">
		<div id="homeHeader">
			<p class="userInfo">
				<img src={{!empty($user->headimgurl) ? $user->headimgurl : url("image/OtherArea.jpg")}} id="wx_image">
				<span id="wx_nickname">{{$user->nickname}}</span>
			</p>
			<p class="account">
				<span></span>
				<span><span>收入/提现<span class="placeholder">占</span></span>></span>
			</p>
		</div>
		<div id="homeTabs">
			<ul class="tabContainer">
				<li class="homeTab active" id="received">
					<span class="icon-iconfont-waitPay icon"></span><br/>
					<span class="tabStatus">已接单</span>
				</li>
				<li class="homeTab" id="waitSend">
					<span class="icon-iconfont-waitSend icon"><span class="catchAttention" style="letter-spacing: -1px;white-space: nowrap;"></span></span><br/>
					<span class="tabStatus">请发货</span>
				</li>
				<li class="homeTab" id="auditing">
					<span class="icon-settings icon"></span><br/>
					<span class="tabStatus">审核中</span>
				</li>				
				<li class="homeTab" id="sent">
					<span class="icon-iconfont-sent icon"></span><br/>
					<span class="tabStatus">已发货</span>
				</li>
				<li class="homeTab" id="completed">
					<span class="icon-iconfont-completed icon"></span><br/>
					<span class="tabStatus">已完成</span>
				</li>
				<hr class="tabFooter"/>
			</ul>
		</div>
		<div id="homeTabsFixed">
			<ul class="tabContainer">
				<li class="homeTab active">
					<span class="icon-iconfont-waitPay icon"></span><br/>
					<span class="tabStatus">已接单</span>
				</li>
				<li class="homeTab">
					<span class="icon-iconfont-waitSend icon"><span class="catchAttention" style="letter-spacing: -1px;white-space: nowrap;"></span></span><br/>
					<span class="tabStatus">请发货</span>
				</li>
				<li class="homeTab">
					<span class="icon-settings icon"></span><br/>
					<span class="tabStatus">审核中</span>
				</li>				
				<li class="homeTab">
					<span class="icon-iconfont-sent icon"></span><br/>
					<span class="tabStatus">已发货</span>
				</li>
				<li class="homeTab">
					<span class="icon-iconfont-completed icon"></span><br/>
					<span class="tabStatus">已完成</span>
				</li>
			</ul>		
		</div>
		<div id="homeContent">
		    <div class="noOrderContent">
		        <img src={{url("/image/order.png")}} class="orderIcon">
		        <p class="orderWord">您还没有相关的订单</p>
		    </div>
		    <div class="orderContent">
		        <div id="orderContent">
		            <ul class="orderContainer">
		            </ul>
		        </div>
		    </div>
		</div>
	</div>

<!-- 商品列表页 -->
	<div id="itemList">
		<div id="itemStatusOuter">
			<table>
				<tr>
					<td><p><span class="postageOuter" style="display: inline-block; float: left;"><span>邮费：</span><span class="postage"></span><span>元</span></span><span class="itemStatus"></span></p></td>
				</tr>
			</table>
		</div>
		<div id="itemListContent">
			<li class="itemContainer" data-item-title="" data-item-description="" data-item-url="" data-item-memos="">
				<table>
					<tbody>
						<tr>
							<td class="itemImageOuter">
								<img src="" class="itemImage">
							</td>
							<td class="itemTitleOuter">
								<p class="itemTitle"></p>
								<p class="itemNumberOuter"><span class="priceOuter" style="display: inline-block; float:left"><span>单价：</span><span class="price"></span><span>元</span></span><sapn style="font-family: sans-serif">x</sapn><span class="itemNumber"></span></p>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
		</div>
		<div id="orderOverview">
			<table>
				<tbody>
					<tr>
						<td class="itemTotalNumberOuter">
							<span class="itemTotalNumber"></span>件商品
						</td>
						<td class="orderPriceOuter">
							<p>合计：￥<span class="orderPrice"></span></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="logistics">
			<div class="receiving_info">
				<p>收货地址：</p>
				<p>
					<span class="receiver"></span>
					<span class="receiver_mobile"></span>
				</p>
				<p>
					<span></span>
					<span class="receiving_address"></span>
				</p>
			</div>
			<div class="logistics_info">
				<p>物流详情</p>
				<p>
					<span>物流公司:</span>
					<span class="logistics_company"></span><br/>
				</p>
				<p>
					<span>物流单号:</span>
					<span class="logistics_number"></span>
				</p>
			</div>
		</div>
		<div id="itemListfooter">
			<div>
				<p><span class="idOuter"></span><span class="id"></span></p>
				<p class="offerTimeOuter"><span>报价时间：</span><span class="offerTime"></span></p>
				<p class="exTimeOuter"><span>有效时间：</span><span class="exTimeInner"></span><span class="exTime">&nbsp;<a>有效时间</a></span></p>
				<p class="payTimeOuter"><span>付款时间：</span><span class="payTime"></span><span class="exSendTime">&nbsp;<a>七日内发货</a></span></p>
				<p class="sendTimeOuter"><span>发货时间：</span><span class="sendTime"></span></p>
				<p class="auditingTimeOuter"><span>审核通过时间：</span><span class="auditingTime"></span></p>
				<p class="completedTimeOuter"><span>完成时间：</span><span class="completedTime"></span></p>
			</div>
		</div>
		<div id="itemListPlaceholder"></div>
		<div id="orderButtonFixed"></div>
	</div>

<!-- 商品详情页 -->
<div id="itemDetail">
    <div id="itemImageContainer">
        <div><img src={{url("image/OtherArea.jpg")}}></div>
    </div>
    <div id="itemTitleContainer">
        <p class="itemTitle"></p>
    </div>
    <div class="itemPlaceholder"></div>
    <div id="itemDescriptionContainer">
        <p class="descriptionTitle">备注</p>

        <p class="itemDescription"></p>

        <p class="descriptionTitle">买手备注</p>

        <p class="opNote"></p>
    </div>
    <div class="itemPlaceholder"></div>
</div>

<!-- 用户资料页 -->
<div id="userInfo">
    <ul class="userInfoContainer">
        <li class="user_imageContainer">
            <table>
                <tbody>
                <tr>
                    <td>
                        <sapn>头像</sapn>
                    </td>
                    <td>
                        <span><img src={{!empty($user->headimgurl) ? $user->headimgurl : url("image/OtherArea.jpg")}} class="user_image"></span>
                        <span class="nextMark placeholder">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li class="user_nicknameContainer">
            <table>
                <tbody>
                <tr>
                    <td><span>昵称</span></td>
                    <td>
                        <span class="user_nickname">{{$user->nickname}}</span>
                        <span class="nextMark placeholder">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li class="user_nameContainer">
            <table>
                <tbody>
                <tr>
                    <td><span>真实姓名</span></td>
                    <td>
                        <span class="user_name">{{$user->seller->real_name}}</span>
                        <span class="nextMark placeholder">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li class="user_countryContainer">
            <table>
                <tbody>
                <tr>
                    <td><span>国家/地区</span></td>
                    <td>
                        <span class="user_country">{{$user->seller->country->name}}</span>
                        <span class="nextMark placeholder">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li class="user_mobileContainer">
            <table>
                <tbody>
                <tr>
                    <td><span>手机号码</span></td>
                    <td>
                        <span class="user_mobile">{{$user->mobile}}</span>
                        <span class="nextMark">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li class="user_emailContainer">
            <table>
                <tbody>
                <tr>
                    <td><span>邮箱</span></td>
                    <td>
                        <span class="user_email">{{$user->email}}</span>
                        <span class="nextMark">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li class="user_accountContainer">
            <table>
                <tbody>
                <tr>
                    <td><span>提现方式管理</span></td>
                    <td>
                        <span class="user_account"></span>
                        <span class="nextMark">&nbsp;></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    </ul>
</div>

<!-- 编辑个人资料页 -->
<div id="editUserInfo">
    <div id="user_mobile" class="editUserInfoContainer">
        <div class="userInfoDetail">
            <table>
                <tbody>
                <tr>
                    <td><p>国家代码</p></td>
                    <td>
                        <input class="user_code" type="tel" placeholder="请输入国家/地区代码">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td><p>手机号码</p></td>
                    <td>
                        <input class="user_mobile" type="tel" placeholder="请输入手机号码">
                    </td>
                    <td>
                        <p class="clear"><span class="icon-cross" style="font-size: 0.8em"></span></p>
                    </td>
                </tr>
                <tr id="captchasContainer">
                    <td><p>验证码</p></td>
                    <td><input type="tel" id="captchas" placeholder="请输入验证码"></td>
                    <td>
                        <p><span id="getCaptchas">获取验证码</span>
                            <span id="countDown"></span></p>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="instruction">*客服将通过手机号码与您联系，请确认手机号码正确</p>
        </div>
        <div class="saveEdit"><p>保存</p></div>
    </div>
    <div id="user_email" class="editUserInfoContainer">
        <div class="userInfoDetail">
            <table>
                <tbody>
                <tr>
                    <td>
                        <p>邮箱</p>
                    </td>
                    <td>
                        <input class="user_email" type="email" placeholder="请输入邮箱">
                    </td>
                    <td>
                        <p class="clear"><span class="icon-cross" style="font-size: 0.8em"></span></p>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="instruction">*客服将通过邮箱与您联系，请确认邮箱正确</p>
        </div>
        <div class="saveEdit"><p>保存</p></div>
    </div>
</div>

<!-- 提现方式管理页 -->
<div id="user_account">
    <?php
    $payment = $user->paymentMethods;
    if(count($payment) == 0){
    $displayAli = "display:none";
    $displayBank = "display:none";
    $name_bank = "空";
    $name_ali = "空";
    $identification_bank = "空";
    $identification_ali = "空";
    $bank_name = "空";
    $ali_payment_id = "";
    $bank_payment_id = "";
    ?>
    <div class="instruction">
        <p>添加银行卡或支付宝才能提现哦~</p>
    </div>
    <?php  }?>
    <div class="payMethod">
        <ul>
            <?php
            if (count($payment) == 1) {
                if ($payment->first()->channel == 1) {
                    $displayAli = "display:none";
                    $displayBank = "";
                    $name_bank = $payment->first()->account_name;
                    $name_ali = "空";
                    $hiddenLength = strlen($payment->first()->identification) - 4;
                    $identification_bank = str_replace(substr($payment->first()->identification, 0, $hiddenLength), str_repeat('*', $hiddenLength), $payment->first()->identification);
                    $bank_name = $payment->first()->bankInfo->bank_name;
                    $identification_ali = "";
                    $bank_payment_id = $payment->first()->payment_methods_id;
                    $ali_payment_id = "";
                } else {
                    $displayAli = "";
                    $displayBank = "display:none";
                    $name_ali = $payment->first()->account_name;
                    $name_bank = "空";
                    $bank_name = "空";
                    $identification_bank = "";
                    $ali_payment_id = $payment->first()->payment_methods_id;
                    $bank_payment_id = "";
                    if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $payment->first()->identification)) {
                        $identification_ali = $payment->first()->identification;
                    } elseif (preg_match("/^1\d{10}$/", $payment->first()->identification)) {
                        $identification_ali = str_replace(substr($payment->first()->identification, 3, 4),
                                str_repeat('*', 4), $payment->first()->identification);
                    }
                }
            } elseif (count($payment) == 2) {
                $displayAli = "";
                $displayBank = "";
                foreach ($payment as $pay) {
                    if ($pay->channel == 1) {
                        $hiddenLength = strlen($pay->identification) - 4;
                        $name_bank = $pay->account_name;
                        $identification_bank = str_replace(substr($pay->identification, 0, $hiddenLength), str_repeat('*', $hiddenLength), $pay->identification);
                        $bank_name = $pay->bankInfo->bank_name;
                        $bank_payment_id = $pay->payment_methods_id;
                    } else {
                        $name_ali = $pay->account_name;
                        $ali_payment_id = $pay->payment_methods_id;
                        if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $pay->identification)) {
                            $identification_ali = $pay->identification;
                        } elseif (preg_match("/^1\d{10}$/", $pay->identification)) {
                            $identification_ali = str_replace(substr($pay->identification, 3, 4),
                                    str_repeat('*', 4), $pay->identification);
                        }
                    }
                }
            }
            ?>
            <li class="alipayOuter" style="{{$displayAli}}" data-payment-id="{{$ali_payment_id}}">
                <table>
                    <tbody>
                    <tr>
                        <td><span class="payment_name">支付宝</span> <span class="icon-bin"></span><span
                                    class="delete">删除</span></td>
                        <td><span class="changeAlipay">更换支付宝</span></td>
                    </tr>
                    <tr>
                        <td><span class="buyer_name">{{$name_ali}}</span><br/><span
                                    class="identification">{{$identification_ali}}</span></td>
                        <?php if(count($payment) == 0){ ?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } elseif(count($payment) == 1){ if($payment->first()->channel == 2 && $payment->first()->is_default == 1){?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } elseif($payment->first()->channel == 2 && $payment->first()->is_default == 0){  ?>
                        <td>
                            <span class="setDefault">设为默认</span>
                        </td>
                        <?php } else{?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } }elseif(count($payment) == 2){ foreach($payment as $pay) { if($pay->channel == 2 && $pay->is_default == 1){?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } elseif($pay->channel == 2 && $pay->is_default == 0) {?>
                        <td>
                            <span class="setDefault">设为默认</span>
                        </td>
                        <?php } } } ?>
                    </tr>
                    </tbody>
                </table>
            </li>
            <li class="bankCardOuter" style="{{$displayBank}}" data-payment-id="{{$bank_payment_id}}">
                <table>
                    <tbody>
                    <tr>
                        <td><span class="payment_name">银行卡</span> <span class="icon-bin"></span><span
                                    class="delete">删除</span></td>
                        <td><span class="changeBankCard">更换银行卡</span></td>
                    </tr>
                    <tr>
                        <td><span class="buyer_name">{{$name_bank}}</span><span
                                    class="bank_info">{{$bank_name}}</span><br/><span
                                    class="identification">{{$identification_bank}}</span></td>
                        <?php if(count($payment) == 0){ ?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } elseif(count($payment) == 1){ if($payment->first()->channel == 1 && $payment->first()->is_default == 1){?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } elseif($payment->first()->channel == 2 && $payment->first()->is_default == 0){  ?>
                        <td>
                            <span class="setDefault">设为默认</span>
                        </td>
                        <?php } else{?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } }elseif(count($payment) == 2){ foreach($payment as $pay) { if($pay->channel == 1 && $pay->is_default == 1){?>
                        <td>
                            <span class="default">默认</span>
                        </td>
                        <?php } elseif($pay->channel == 1 && $pay->is_default == 0) {?>
                        <td>
                            <span class="setDefault">设为默认</span>
                        </td>
                        <?php } } } ?>
                    </tr>
                    </tbody>
                </table>
            </li>
        </ul>
    </div>
    <?php if(count($payment) == 0) { ?>
    <div class="editPayMethod">
        <div class="addNewPayMethod addAlipay">
            <p>添加支付宝</p>
        </div>
    </div>
    <div class="editPayMethod">
        <div class="addNewPayMethod addBankCard">
            <p>添加银行卡</p>
        </div>
    </div>
    <?php } elseif(count($payment) == 1) {  if($payment->first()->channel == 1) { ?>
    <div class="editPayMethod">
        <div class="addNewPayMethod addAlipay">
            <p>添加支付宝</p>
        </div>
    </div>
    <?php } elseif($payment->first()->channel == 2) { ?>
    <div class="editPayMethod">
        <div class="addNewPayMethod addBankCard">
            <p>添加银行卡</p>
        </div>
    </div>
    <?php } } elseif (count($payment) == 2) { ?>
		<?php } ?>
</div>

<!-- 添加支付宝页 -->
	<div id="addAlipay">
		<div class="addDetail">
			<p>支付宝账号</p>
			<p><span class="icon-credit-card"> </span><input type="email" placeholder="请填写您的支付宝账号"><br/><span class="icon-credit-card"> </span><input type="email" placeholder="请确认您的支付宝账号"></p>
			<p>支付宝账号实名</p>
			<p><span class="icon-user"> </span><input type="text" placeholder="请填写支付宝账号对应的中文实名"></p>
			<p>安全密码<span class="placeholder">00</span><a href="/seller/resetSecurePassword">忘记密码</a></p>
			<p><span class="icon-key"> </span><input type="password" placeholder="请输入您的安全密码"></p>
		</div>
		<div class="saveOuter">
			<div class="save">
				<p>保存</p>
			</div>
		</div>
	</div>

<!-- 添加银行卡页 -->
	<div id="addBankCard">
		<div class="addDetail">
			<p>银行卡号</p>
			<p><span class="icon-credit-card"> </span><input type="tel" placeholder="请填写您的银行卡号"><br/><span class="icon-credit-card"> </span><input type="tel" placeholder="请确认您的银行卡号"></p>
			<p>银行卡账户名</p>
			<p><span class="icon-user"> </span><input type="text" placeholder="必须为银行卡中文实名，否则无法提现"></p>
			<p>安全密码<span class="placeholder">00</span><a href="/seller/resetSecurePassword">忘记密码</a></p>
			<p><span class="icon-key"> </span><input type="password" placeholder="请输入您的安全密码"></p>
		</div>
		<div class="saveOuter">
			<div class="save">
				<p>保存</p>
			</div>
		</div>
	</div>

<?php
$seller_id = $user->seller->seller_id;
$suborders_audits = App\Models\SubOrder::where('seller_id', $seller_id)->whereIn('sub_order_state', [501, 521])->get()->sortByDesc('updated_at');
$suborders_waits = App\Models\SubOrder::where('seller_id', $seller_id)->whereIn('sub_order_state', [301, 601])
        ->get()->sortByDesc('updated_at');
$suborders_withdraws = App\Models\SubOrder::where('seller_id', $seller_id)->where('withdraw_state', '>', 0)->get()->sortByDesc('updated_at');
$price = 0;
$price_withdraw = 0;
$price_audit = 0;
foreach ($suborders_audits as $audit) {
    $price_audit += $audit->sub_order_price - $audit->refund_price;
}
foreach ($suborders_waits as $wait) {
    $price += $wait->transfer_price;
}
foreach ($suborders_withdraws as $withdraw) {
    $price_withdraw += $withdraw->transfer_price;
}
?>
<!-- 收入/提现页 -->
	<div id="withdrawals">
		<div>
			<img src={{$user->headimgurl}}>
			<p>累计交易流水</p>
			<p class="totalRevenue">
				<small>￥</small><span>{{sprintf('%.2f',$price+$price_audit)}}</span>
			</p>
		</div>
		<div>
			<ul>
				<li class="toSend">
					<p>未入账总额</p>
					<p class="waitRevenue">
						<small>￥</small><span>{{sprintf('%.2f',$price_audit)}}</span>
					</p>
					<p><small class="minword">发货后通过审核可以提现</small></p>
				</li>
				<li class="toRevenue">
					<p>我的累计收入</p>
					<p class="revenue">
						<small>￥</small><span>{{sprintf('%.2f',$price)}}</span>
					</p>
					<p><small class="minword">申请提现后3个工作日内到账</small></p>
				</li>
			</ul>
		</div>
		<div>
			<table class="account">
				<tbody>
					<tr>
						<td><span></span></td>
						<td>
							<span class="user_account"></span>
							<span class="nextMark"><span>提现方式管理<span class="placeholder">占</span></span>></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<!-- 未入账总额页 -->
<div id="waitRevenue">
    <ul class="tabContainer">
        <li class="active"><span class="toWaitSend">未发货订单</span></li>
        <li><span class="toAuditing">发货审核中</span></li>
    </ul>
    <ul class="orderContainer">
    </ul>
</div>

<!-- 我的累计收入 -->
<div id="revenue">
    <ul class="tabContainer">
        <li class="active"><span class="toGoing">可提现订单</span></li>
        <li><span class="toCompleted">提现记录</span></li>
    </ul>
    <ul class="going">
    </ul>
    <ul class="completed">
    </ul>
</div>

<!-- 弹窗页 -->
<!-- 确认提现弹窗 -->
	<div id="sureToWithdrawals">
		<table>
			<tr>
				<td>
					<p>提现至<span class="payment_name"></span>：<span class="identification"></span>(<span class="buyer_name"></span>)</p>
					<p>提现金额：<span class="amount"></span><span>元</span></p>
				</td>
			</tr>
			<tr>
				<td>
					<p style="text-align: center;">
						<span class="keepItOn">取消</span>
						<span class="sureToWithdrawals" data-order-id>确认</span>
					</p>
				</td>
			</tr>
		</table>
	</div>
<!-- 设为默认提现方式页 -->
<div id="sureToSetDefault">
    <table>
        <tr>
            <td>
                <p>将<span class="payment_name"></span>设为默认提现方式，提现时，款项默认打入该账户。<br/>是否继续？</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span class="keepItOn">取消</span>
                    <span class="sureToSetDefault" data-payment-id>确认</span>
                </p>
            </td>
        </tr>
    </table>
</div>
<!-- 取消订单确认页 -->
<div id="cancleOrder">
    <table>
        <tr>
            <td>
                <p style="text-align: left">拒单会影响你的信用度，请谨慎！如有疑问，请联系客服。</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span class="keepItOn">取消</span>
                    <span class="sureToCancle" data-order-id data-order-index>确认</span>
                </p>
            </td>
        </tr>
    </table>
</div>
<!-- 联系客服页 -->
<div id="callOp">
    <table>
        <tr>
            <td>
                <p>客服电话/微信：<span class="opTel"></span></p>

                <p>(客服工作时间：9:00-21:00)</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span class="keepItOn">取消</span>
                    <span class="call"><a href="">拨打</a></span>
                </p>
            </td>
        </tr>
    </table>
</div>
<!-- 价格有效时间页	 -->
<div id="exTime">
    <table>
        <tr>
            <td>
                <p>红领巾施行一口价原则，报价后，价格不受汇率变动等因素影响；若买家未在报价后72小时内付款，则订单失效。</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span class="keepItOn">我知道啦</span>
                </p>
            </td>
        </tr>
    </table>
</div>
<!-- 价格有效时间页	 -->
<div id="exSendTime">
    <table>
        <tr>
            <td>
                <p>买手需在买家付款后7日内发货，否则会影响你的信用度。如有疑问，请联系客服。</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span class="keepItOn">我知道啦</span>
                </p>
            </td>
        </tr>
    </table>
</div>
<!-- 发货填写物流单号页 -->
<div id="send">
    <table>
        <tr>
            <td>
                <p class="logisticsOuter">
                    <select class="logistics needsclick needsfocus">
                        <?php $companies = App\Models\DeliveryCompany::all(); ?>
                        <option value="0">请选择快递公司</option>
                        @foreach($companies as $company)
                            <option value="{{$company->delivery_company_id}}"
                                    data-pinyin="{{$company->pinyin}}">{{$company->company_name}}</option>
                        @endforeach
                        <option value="otherCompany">其他快递公司</option>
                    </select>
                </p>
                <p><input class="logistics_other" type="text" placeholder="请填写快递公司名称"></p>

                <p><input class="logistics_number" type="text" placeholder="请填写快递单号"></p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span class="keepItOn">取消</span>
                    <span class="sureToSend" data-order-id>确认</span>
                </p>
            </td>
        </tr>
    </table>
</div>

<script src="http://7xnzm2.com2.z0.glb.qiniucdn.com/sellerOrder.min.js"></script>
<script type="text/javascript">
    if (window.location != window.parent.location) window.parent.location = window.location;
</script>

</body>
</html>