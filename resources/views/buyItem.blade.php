<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
	<title>我要买</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href={{url("css/style.css")}}>
	<link rel="stylesheet" href={{url("css/buyItem.css")}} />
	<div id='wx_pic' style='margin:0 auto;display:none;'>
		<img src={{url("image/LOGO.jpg")}} />
	</div>
</head>
<body>

	<div id="loading">
		<div class="spinner">
  			<div class="bounce1"></div>
  			<div class="bounce2"></div>
		</div>
	</div>
	
	<div class="pure-g animated" id="pagetwo" style="display: block">
		<div class="pure-u-1-1 pagetitle">
			<p>
				<span id="countrychose">
					<span class="countrychose">日本</span>
				</span>
				<span id="priceOverview">
					<span class="totalPrice"><small>￥</small>150.00</span>
				</span>
			</p>
		</div>
		<div class="pure-u-1-1 itemtitle">
			<li class="item0">
				<div class="itemtitle1">
					<p>篮球大篮球</p>
				</div>
				<div class="itemtitle2">
					<table>
						<tr>
							<td><span class="number">3</span><span>个</span></td>
						</tr>
					</table>
				</div>
			</li>
		</div>
		<div class="pure-u-1-1 pagefooter">
			<div class="pagefooter1"><p>从<span class="countrychose">日本</span>代购<span id="totalnumber">3</span>件商品</p></div>
			<div class="submit"><p>提交订单</p></div>
		</div>
		<div class="background"></div>
	</div>

	<div class="pure-g animatedshort" id="pagethree">
		<div class="itemdetail" id="item0">
			<div class="pure-u-1-1 pagetitle">				
				<div class="saveouter">
					<span class="icon-checkmark"></span>
				</div>
			</div>
			<div class="pure-u-1-1 title">
				<span>商品名称</span>
				<textarea class="newtitle" readonly="true">这里是商品名称</textarea>
			</div>
			<div class="pure-u-1-1 numberAndPrice">
				<table>	
					<tr>
						<td>
							<span>总价</span>
						</td>
						<td>
							<span>￥</span><span class="totalPrice">150.00</span>
						</td>
					</tr>
				</table>				
			</div>
			<div class="pure-u-1-1 description">
				<span>备注</span>
				<textarea class="newdescription" readonly="true">这里是商品备注</textarea>
			</div>
			<div class="pure-u-1-1 picture">
				<p><span>商品图片</span></p>
				<div class="picturearea">
					<div class="picturescroll">
						<div class="pictureshow">
							<img src={{url("image/LOGO.jpg")}} alt="" />
						</div>
						<div class="pictureshow">
							<img src={{url("image/LOGO.jpg")}} alt="" />
						</div>
						<div class="pictureshow">
							<img src={{url("image/LOGO.jpg")}} alt="" />
						</div>
					</div>																				
				</div>
			</div>
		</div>
	</div>

	<div class="pure-g animated" id="pagefour">
		<div class="pure-u-1" id="headerfour"><span class="icon-cross"></span></div>
		<div class="pure-u-1" id="finalnumber"><p>
			<span>您将从</span><span class="country">日本</span><span>代购</span><span class="number">3</span><span>件商品；</span><br /><span>总价为</span><span class="totalPrice">150</span><sapn>元。</sapn>
		</p></div>
		<div class="pure-u-1" id="makesure"><span id="submit">确认提交</span></div>
	</div>

	<div class="pure-g animatedshort" id="pagesix">
		<div class="pure-u-1" id="headersix">
			<span class="icon-cross"></span>
			<span class="icon-plus placeholder"></span>
			<img src={{url("image/OtherArea.jpg")}} id="wx_image">
			<p id="wx_nickname">哈哈哈哈</p>
		</div>
		<div class="pure-u-1" id="contentsix">
			<form class="pure-form pure-form-aligned">
				<fieldset>	
					<legend>
						<span class="icon-phone mark"></span>
						<input id="mobile" type="tel" name="mobile" placeholder="请输入您的手机号"/>
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
					</legend>
				</fieldset>
				<div id="registerOuter">
					<span id="register">注 册</span>
				</div>
			</form>
		</div>
	</div>

	<div class="animated" id="pageseven">
		<div id="headerseven">
			<div>
         		<span class="icon-checkmark"></span>
    		</div>
		</div>
		<div id="contentseven">
			<p>您已成功从<span class="country">日本</span>代购<span class="number">3</span>件商品。<span>总价为</span><span class="totalPrice">150</span>元。请耐心等待客服和您联系。\^o^/</p>
		</div>
		<div id="footerseven">
			<span class="goShopping">继续购物</span>
			<span class="myOrder">我的订单</span>
		</div>
	</div>

</body>
	<script src="https://cdn1.lncld.net/static/js/av-mini-0.5.4.js"></script>
	<script src="https://cdn1.lncld.net/static/js/av-core-mini-0.5.4.js"></script>
	<script src={{url("js/zepto.min.js")}}></script>
	<script src={{url("js/buyItem.js")}}></script>
</html>