<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
	<title>我要买</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href="{{url('css/buy.min.css')}}">
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
	
	<div class="pure-g animated" id="pageone">
		<div class="pure-u-1-1 pagetitle">
			<p>
				<span>请选择代购的国家/地区</span>
			</p>
		</div>
		<div class="pure-g pagecontent">
			<div class="pure-u-1-3">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/HongKong.jpg" id="HongKong" class="countryimg">
				<div class="countryname"><p>香港</p></div>
			</div>
			<div class="pure-u-1-3">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/Japan.jpg" id="Japan" class="countryimg">
				<div class="countryname"><p>日本</p></div>
			</div>
			<div class="pure-u-1-3 last">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/Korea.jpg" id="Korea" class="countryimg">
				<div class="countryname"><p>韩国</p></div>
			</div>
			<div class="pure-u-1-3">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/America.jpg" id="America" class="countryimg">
				<div class="countryname"><p>美国</p></div>
			</div>
			<div class="pure-u-1-3">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/Australia.jpg" id="Australia" class="countryimg">
				<div class="countryname"><p>澳大利亚</p></div>
			</div>
			<div class="pure-u-1-3 last">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/Germany.jpg" id="Germany" class="countryimg">
				<div class="countryname"><p>德国</p></div>
			</div>
			<div class="pure-u-1-3">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/England.jpg" id="England" class="countryimg">
				<div class="countryname"><p>英国</p></div>
			</div>
			<div class="pure-u-1-3">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/French.jpg" id="French" class="countryimg">
				<div class="countryname"><p>法国</p></div>
			</div>
			<div class="pure-u-1-3 last">
				<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/OtherArea.jpg" id="OtherArea" class="countryimg">
				<div class="countryname"><p>任意国家</p></div>
			</div>
		</div>
		<div class="pure-u-1-1 pagefooter">
			<div class="footerinformation"><p>当前选择国家：<span class="countryindex"></span></p></div>
			<div id="checkmarkouter">
				<p id="checkmark" class="animated">下一步</p>
			</div>
		</div>
	</div>

	<div class="pure-g animated" id="pagetwo">
		<div class="pure-u-1-1 pagetitle">
			<p>
				<span id="countrychose">
					<span class="countrychose"></span>
				</span>
			</p>
			<div id="additemouter">
				<span id="additem" class="icon-plus catchAttention"></span>
			</div>
		</div>
		<div class="pure-u-1-1 itemtitle">
			<div class="guide">
				<p>点击右上角 <span class="icon-plus"></span> ，添加一件商品</p>
			</div>
			<li class="item0" style="display:none;">
				<div class="itemtitle1">
					<p>商品标题到底有多少</p>
				</div>
				<div class="itemtitle2">
					<table>
						<td class="minustd"><p class="numberminus">-</p></td>
						<td class="showtd"><p class="numbershow">1</p></td>
						<td class="addtd"><p class="numberadd">+</p></td>
					</table>
				</div>
				<div class="itemtitle3">
					<p><span class="icon-bin animated"></span><span class="delete animated">删除</span></p>
				</div>
			</li>
		</div>
		<div class="pure-u-1-1 pagefooter">
			<div class="pagefooter1"><p>从<span class="countrychose"></span>代购<span id="totalnumber">0</span>件商品</p></div>
			<div class="submit"><p>提交订单</p></div>
		</div>
		<div class="background"></div>
	</div>

	<div class="pure-g animatedshort" id="pagethree">
		<div class="itemdetail" id="item0">
			<div class="pure-u-1-1 pagetitle">
				<div class="closeouter">
					<span class="icon-bin animated"></span>
				</div>
				<div class="saveouter">
					<span class="icon-checkmark"></span>
				</div>
			</div>
			<div class="pure-u-1-1 title">
				<span>商品名称</span>
				<textarea placeholder="点此输入您想代购的商品（必填）" class="newtitle"></textarea>
			</div>
			<div class="pure-u-1-1 number">
				<table>	
					<td class="numbertitle"><span>数量</span></td>
					<td class="minustd"><p class="numberminus">-</p></td>
					<td class="showtd"><p class="numbershow">1</p></td>
					<td class="addtd"><p class="numberadd">+</p></td>
				</table>
			</div>
			<div class="pure-u-1-1 description">
				<span>备注</span>
				<textarea placeholder="点此输入官网链接等" class="newdescription"></textarea>
			</div>
			<div class="pure-u-1-1 picture">
				<p><span>图片</span></p>
				<div class="picturearea">
					<div class="pictureex" data-src>
						<img src="" alt="" />
						<div class="picturedelete">
							<span class="icon-cross"></span>
						</div>
					</div>
					<div class="picturescroll" style="height:100%">
						<div class="chosefile" >
							<!-- <input type="file" class="pickfiles"/> -->
							<span class="icon-plus"></span>
						</div>
						<i>添加商品图片，更快获得反馈</i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="pure-g animated" id="pagefour">
		<div class="pure-u-1" id="headerfour"><span class="icon-cross"></span></div>
		<div class="pure-u-1" id="finalnumber"><p></p></div>
		<div class="pure-u-1" id="makesure"><span id="submit">确认</span></div>
	</div>

	<div class="pure-g animated" id="pagefive">
		<div class="pure-u-1" id="headerfive"><span class="icon-cross"></span></div>
		<div class="pure-u-1" id="contentfive"><p>返回重选国家/地区，已添加的商品信息将被删除。<br />确认重选国家/地区？</p></div>
		<div class="pure-u-1" id="footerfive">
			<div id="cancle"><span>取消</span></div>
			<div id="suretochange"><span>确认</span></div>
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

	<div class="animated" id="pageseven">
		<div id="contentseven">
			<div>
         		<span class="icon-checkmark"></span>
    		</div>
			<p>您已成功提交代购需求。<br />
				客服将在24小时内反馈是否有货，请留意电话/短信/邮件通知。
			</p>
		</div>
		<div id="footerseven">
			<span class="myOrder">查看我的订单</span>
		</div>
	</div>

</body>
	<script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/buy.min.js"></script>
</html>