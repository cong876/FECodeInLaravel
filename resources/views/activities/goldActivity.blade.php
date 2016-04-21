<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>集星星换奖品</title>
	<link rel="stylesheet" type="text/css" href="{{url('css/goldActivity.min.css')}}">
</head>
<body>

    <?php $user = Auth::user() ?>
	<div id="loading">
		<div class="spinner">
  			<div class="bounce1"></div>
  			<div class="bounce2"></div>
		</div>
	</div>

	<div id="data" data-init="{{$activityData}}" data-sender="{{$user->openid}}"></div>

<!-- 主页 -->
	<div id="main">
		<div id="header">
			<table>
				<tbody>
					<tr>
						<td colspan="1" rowspan="2" class="headImageOuter"><img class="headImage" src="{{!empty($user->headimgurl) ? $user->headimgurl : url('image/OtherArea.jpg')}}"></td>
						<td colspan="2" rowspan="1" class="nickNameOuter"><h3 class="nickName">{{!empty($user->nickname) ? $user->nickname : "红领巾"}}</h3></td>
					</tr>
					<tr>
						<td class="starsNumberOuter"><img class="bigStar" src="{{url('image/star.png')}}"><span>&times;</span><span class="starsNumber"></span></td>
						<td class="addStarOuter"><button class="addStar">我要小星星</button></td>
					</tr>
<!-- 					<tr>
						<td colspan="3" class="guideOuter"><span class="toFriends">谁打赏过我 <strong>>></strong></span></td>
					</tr> -->
				</tbody>
			</table>
		</div>

		<div id="content">
			<ul>
				<li class="itemEx">
					<table cellspacing="0" cellpadding="0">
						<tbody>
							<tr>
								<td rowspan="2" class="itemImageOuter"><img class="itemImage" src="{{url('image/America.jpg')}}"></td>
								<td colspan="2" class="itemInfoOuter">
									<div class="itemInfoInner">
										<h4 class="itemTitle"></h4>
										<p class="itemDescription"></p>
									</div>
								</td>
							</tr>
							<tr>
								<td class="priceOuter">
									<p><span class="priceInner"><img class="smallStar" src="{{url('image/star.png')}}"><span>&times;</span><span class="price"></span></span></p>
									<p><span class="marketPriceOuter"><span class="marketPriceName">市场价</span>:￥<span class="marketPrice"></span></span></p>
								</td>
								<td class="getOuter"><button class="getFree" data-item="">免费领取</button></td>
							</tr>
						</tbody>
					</table>
				</li>
			</ul>
		</div>
		
		<div id="footer">
			<section class="rules">
				<div class="rulesTitle">活动说明</div>
				<img class="rulesContent" src="{{url('image/starRule.png')}}">
			</section>
			<section class="friends">
				<div class="friendsTitle"><span class="title_dot">▼</span>我的小伙伴</div>
				<div class="friendsContainer">
				</div>
				<div class="loadMore">
					<button class="checkMore">查看更多</button>
					<div id="smallLoading">
						<div class="spinner">
				  			<div class="bounce1"></div>
				  			<div class="bounce2"></div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>


<!-- 提示分享弹窗 -->
	<div id="toShare" class="modal">
		<div class="inner">
			<section class="shareGuideImageOuter">
				<img src="{{url('image/starModalBg.jpg')}}">
				<div class="fromIwanna">小星星不够啦，<br/>快去找朋友打赏小星星~</div>
			</section>
			<section class="shareGuide">
				点击【右上角】转给您的朋友<br/>朋友打开该页面并关注公众号<br/>获得小星星(^ω^)<br/>
				<button class="close toAddStar">知道啦</button>
			</section>
		</div>
	</div>


<!-- 兑换弹窗 -->
	<div id="toGet" class="modal"  data-address-id="" data-item="">
		<div class="inner">
			<div class="closeOuter"><span class="close">&times;</span></div>
			<div class="modalContent">
				<section class="addressContainer">
					<p>收货人：<span class="receiver"></span>，<span class="receiver_mobile"></span></p>
					<p><span class="justMark">></span><span class="receiver_address">点击添加收货地址</span></p>
				</section>
				<hr/>
				<section class="itemInfoContainer">
					<table>
						<tbody>
							<tr>
								<td><img class="itemImage" src=""></td>
								<td>
									<h4 class="itemTitleOuter"></h4>
									<span class="numberName">数量</span>
									<p class="itemNumberOuter"><span class="subNumber">-</span><span class="itemNumber">1</span><span class="addNumber">+</span></p>
								</td>
							</tr>
						</tbody>
					</table>
				</section>
				<p class="limitedExplain">占</p>
				<hr class="split"/>
				<section class="starsContainer">
					<table>
						<tbody>
							<tr>
								<td class="smallOuter"><small>*兑换的奖品不能退换</small></td>
								<td class="priceInner">合计:<img src="{{url('image/star.png')}}" class="smallStar"><span>&times;</span><span class="itemStars">5</span></td>
							</tr>
						</tbody>
					</table>
				</section>
				<section class="buttonContainer"><button class="getFree">确认兑换</button></section>
			</div>
		</div>
	</div>

<!-- 编辑地址页 -->
	<div id="editUserAddress">
		<div>
			<table class="addressDetail">
				<tbody>
					<tr>
						<td><label for="receiver"><span>收货人</span></label></td>
						<td>
							<input id="receiver" class="receiver" type="text" placeholder="点此输入收货人姓名">
						</td>
					</tr>
					<tr>
						<td>
							<label for="receiver_mobile"><span>手机号码</span></label>
						</td>
						<td>
							<input id="receiver_mobile" class="receiver_mobile" type="tel" placeholder="点此输入收货人手机号">
						</td>
					</tr>
					<tr>
						<td>
							<label for="province"><span>所在省/市</span></label>
						</td>
						<td>
							<select name="province" id="province" class="needsclick needsfocus">
								<option value="0">--请选择--</option>
								<?php $provinces = DB::table('china_regions')->where('level',1)->get() ?>
								@foreach($provinces as $province)
								<option value="{{$province->code}}">{{$province->name}}</option>
								@endforeach
							</select>							
						</td>
					</tr>
					<tr>
						<td>
							<label for="city"><span>所在市</span></label></td>
						<td>
							<select name="city" id="city" class="needsclick needsfocus">
							</select>							
						</td>
					</tr>
					<tr>
						<td>
							<label for="coynty"><span>所在区/县</span></label>
						</td>
						<td>
							<select name="county" id="county" class="needsclick needsfocus">
							</select>							
						</td>
					</tr>
					<tr>
						<td>
							<label for="receiver_address"><span>街道地址</span></label>
						</td>
						<td>
							<input id="receiver_address" class="receiver_address" type="text" placeholder="点此输入详细地址">
						</td>
					</tr>
					<tr>
						<td>
							<label for="receiver_zipCode"><span>邮政编码</span></label>
						</td>
						<td>
							<input id="receiver_zipCode" class="receiver_zipCode" type="tel" placeholder="点此输入邮编">
						</td>
					</tr>
				</tbody>
			</table>
			<div>
				<table class="setAddressContainer">
					<tr>
						<td class="setDefaultAddress setAddress">
							<span id="setDefaultAddress" class="icon-radio-checked"></span><span>设为默认收获地址</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="saveAddressOuter">
			<div id="saveAddress">
				<p>保存收货地址</p>
			</div>
		</div>				
	</div>

<!-- 收货地址列表	 -->
	<div id="receiving_addresses">
		<div class="addressDirect">点击选择收货地址</div>
		<div class="editAddressesContent">
			<li class="user_AddressEx" data-address="">
				<table>
					<tbody>
						<tr>
							<td><span class="default"></span><span class="receiver"></span></td>
							<td><span class="receiver_mobile"></span></td>
						</tr>
						<tr>
							<td colspan="2">
								<span class="receiver_address"></span>
							</td>
						</tr>
					</tbody>
				</table>					
			</li>
		</div>
		<div class="editAddressesFooter">
			<div class="addNewAddress">
				<p>添加收货地址</p>
			</div>
		</div>		
	</div>

<!-- 注册页 -->
	<div class="pure-g animatedshort" id="pagesix">
		<div class="pure-u-1" id="headersix">
			<span class="icon-cross"></span>
			<span class="icon-plus placeholder"></span>
			<img src="{{!empty($user->headimgurl) ? $user->headimgurl : url("image/OtherArea.jpg")}}" id="wx_image">
			<p id="wx_nickname">{{!empty($user->nickname) ? $user->nickname : "红领巾"}}</p>
		</div>
		<div class="pure-u-1" id="contentsix">
			<form class="pure-form pure-form-aligned">
				<legend>
					<span class="icon-phone mark"></span>
					<input id="mobile" type="tel" name="mobile" placeholder="请输入您的手机号"/>
					<span class="icon-cross clear"></span>
				</legend>
				<legend style="margin-bottom: 2em">
					<span class="icon-paperplane mark"></span>
					<input id="captchas" type="tel" name="captchas" placeholder="请输入验证码"/>
					<span id="sendcaptchas">获取验证码</span>
					<span id="countDown"></span>
				</legend>
				<legend style="margin-bottom: 1em">
					<span class="icon-mail mark" id="mailMark"></span>
					<input id="email" type="email" name="email" placeholder="请输入您的常用邮箱"/>
					<span class="icon-cross clear"></span>
				</legend>
				<div id="registerOuter">
					<span id="register">注 册</span>
				</div>
			</form>
		</div>
	</div>

<!-- 最终提交的数据 -->
	<form action="createGoldOrder" method="post" style="display: none" id="finalData">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="text" name="item_id">
		<input type="number" name="number">
		<input type="number" name="gold">
		<input type="text" name="receiving_address_id">
	</form>

    <?php

	$appId  = config('wx.appId');
	$secret = config('wx.appSecret');
    $js = new \Overtrue\Wechat\Js($appId, $secret);

    ?>

	<!-- // <script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/goldActivity.min.js"></script> -->
	<script type="text/javascript" src="{{url('js/goldActivity.min.js')}}"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
        wx.config(<?php echo $js->config(array('onMenuShareAppMessage','hideMenuItems'), false, true) ?>);
        wx.ready(function(){

        	var sender = {
        		nickname: $(".nickName").eq(0).text(),
        		open_id: $("#data").data("sender")
        	};
        	var link = "http://www.yeyetech.net/accessGoldActivity";

			wx.hideMenuItems({
			    menuList: ["menuItem:share:timeline"] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
			})

            wx.onMenuShareAppMessage({
                title: "点我给【" + sender.nickname + "】打赏小星星~", // 分享标题
                link: link+"?optaskid=1"+"&sender="+sender.open_id, // 分享链接  
                desc: "收集小星星，海外商品免费领！", // 分享描述
                imgUrl: "http://7xln8l.com2.z0.glb.qiniucdn.com/zhuanfafenxiangxiaotu.jpg", // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
	</script>

</body>
</html>