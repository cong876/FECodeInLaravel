<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>今日团购</title>
	<link rel="stylesheet" type="text/css" href="{{url('css/dailyActivity.min.css')}}">
</head>
<body>

	<div id="loading">
		<div class="spinner">
  			<div class="bounce1"></div>
  			<div class="bounce2"></div>
		</div>
	</div>

	<div id="data" data-activity="{{$activityData}}"></div>

	<div class="slide" id="carousel">
	    <ul>
	        <li><a href=""><img src=""></a></li>
	    </ul>
	    <div class="dot">
	        <span class="cur"></span>
	    </div>
	</div>

	<div id="header">
		<section class="fontOne" style="height: 3em">
			<ul>
				<li class="tabsContainer"><span class="tabs active">今日团购</span></li>
				<li class="splitMark"><hr style="border: 1px solid rgb(182,182,182)"></li>
				<li class="tabsContainer"><span class="tabs">明日预告</span></li>
			</ul>
		</section>
		<section class="activityImageContainer">
			<img class="activityImage" src="">
			<div class="toBeContinued fontColorFour">敬请期待！</div>
		</section>
		<section class="countDownContainer fontOne">
			<span>距团购结束</span>
			<span id="countDownIndex">
				<span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>
			</span>
			<span id="countDownNext">
				<span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>
			</span>
		</section>
	</div>
	<div id="content">
		<li class="item" id="item0" data-id="" data-limitedNumber="">
			<section>
				<span class="fontOne fontColorFour itemTitleContainer">
					<span class="orderNumber"></span>
					<span class="itemTitle"></span>
				</span>
			</section>
			<section class="itemDescriptionContainer fontTwo"></section>
			<section class="itemImageContainer">
				<img src="" class="itemImage">
			</section>
			<section class="itemPriceContainer fontTwo">
				<table>
					<tbody>
						<tr>
							<td><span class="fontFive fontColorFour">￥<span class="itemPrice"></span></span>  <span class="fontColorFour" style="border:1px solid currentColor;">包邮</span><br><span style="color: gray;text-indent:0.3em;display:inline-block;">国内￥<span class="marketPrice"></span></span></td>
							<td><button class="buyNow fontOne">立即购买</button></td>
						</tr>
					</tbody>
				</table>
			</section>
		</li>
	</div>
	<div id="footer">
		<section class="introduce fontFour">
			<p class="fontThree">温馨提示</p>
			<p>红领巾是您的<span class="fontColorFour">私人代购助手</span></p>
			<p>能够为您代购<span class="fontColorFour">任何国家</span>的<span class="fontColorFour">任何商品</span></p>
			<p>▼</p>
			<img src="{{url('image/buyForMe.jpg')}}" class="buyForMe">
			<p>公众号菜单栏点击“帮我代购”</p>
			<p>提交您的代购需求清单</p>
			<p>客服将在24小时内反馈价格</p>
			<p>请留意电话/短信/邮件通知</p>
		</section>
		<section class="copyRight fontFour">
			©2016 北京耶烨共享科技有限公司
		</section>
	</div>

	<div id="buyModal">
		<div class="modal">
			<section class="modalHeader"><span class="close">&times;</span></section>
			<section class="itemInfoContainer">
				<table>
					<tbody>
						<tr class="itemInfo">
							<td rowspan="2" class="itemImageModal"><img src="{{url('image/Korea.jpg')}}"></td>
							<td colspan="2" class="itemTitleModal"></td>
						</tr>
						<tr class="itemPriceAndNumber">
							<td colspan="1">￥<span class="itemPriceModal"></span></td>
							<td colspan="1" class="itemNumberContainerModal">
								<table>
									<tbody>
										<tr>
											<td class="numberSub"><span>-</span></td>
											<td class="itemNumberModal"><span>1</span></td>
											<td class="numberAdd"><span>+</span></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr class="markContainer fontColorFour">
							<td colspan="3"><span class="limitedMark">*本商品限购<span class="limitedNumber"></span>件</span></td>
						</tr>
					</tbody>
				</table>
			</section>
			<section class="itemNoteContainer fontColorOne">
				<span class="forItemNote">备注：</span>
				<textarea name="itemNote" class="itemNote" rows="5" placeholder="选填" style="color:#000"></textarea>
			</section>
			<section class="modalFooter">
				合计：<span class="fontColorFour">￥<span class="totalPrice"></span>(包邮)</span>
				<button class="fontOne sureToBuy" style="padding-left:1em; padding-right: 1em">确  定</button>
			</section>
			<form action="{{url('/createGroupOrder')}}" method="post" style="display: none" id="finalData">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="text" name="item_id">
				<input type="text" name="order_memo">
				<input type="number" name="number">
				<input type="number" name="price">
			</form>
		</div>
	</div>

    <?php $user = Auth::user() ?>
	<div class="pure-g animatedshort" id="pagesix">
		<div class="pure-u-1" id="headersix">
			<span class="icon-cross"></span>
			<span class="icon-plus placeholder"></span>
			<img src="{{!empty($user->headimgurl) ? $user->headimgurl : url("image/OtherArea.jpg")}}" id="wx_image">
			<p id="wx_nickname">{{$user->nickname}}</p>
		</div>
		<div class="pure-u-1" id="contentsix">
			<form class="pure-form pure-form-aligned">
				<fieldset>
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
				</fieldset>
				<div id="registerOuter">
					<span id="register">注 册</span>
				</div>
			</form>
		</div>
	</div>

    <?php
    $appId  = config('wx.appId');
    $secret = config('wx.appSecret');
    $js = new \Overtrue\Wechat\Js($appId, $secret);

    ?>

	<!-- // <script type="text/javascript" src="{{url('js/dailyActivity.min.js')}}"></script> -->
	<script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/dailyActivity.min0003.js"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
        var dataAll = JSON.parse($("#data").data("activity"));
        wx.config(<?php echo $js->config(array('onMenuShareAppMessage','onMenuShareTimeline'), false, true) ?>);
        wx.ready(function(){

            wx.onMenuShareTimeline({
                title: dataAll.share.share_title, // 分享标题
                link: location.href+"?title="+dataAll.share.share_title+"&description="+dataAll.share.share_description+"&imgUrl="+dataAll.share.share_url, // 分享链接
                imgUrl: dataAll.share.share_url, // 分享图标
                success: function () { 
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () { 
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareAppMessage({
                title: dataAll.share.share_title, // 分享标题
                link: location.href+"?title="+dataAll.share.share_title+"&description="+dataAll.share.share_description+"&imgUrl="+dataAll.share.share_url, // 分享链接  
                desc: dataAll.share.share_description, // 分享描述
                imgUrl: dataAll.share.share_url, // 分享图标
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