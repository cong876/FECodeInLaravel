<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
	<title>红领巾商城</title>
	<link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" href={{url("css/style.css")}}>
	<link rel="stylesheet" type="text/css" href={{url("MyOrder")}}>
	<div id='wx_pic' style='margin:0 auto;display:none;'>
		<img src={{url("image/LOGO.jpg")}} />
	</div>
</head>
<body>

	<div id="home">
		<div id="homeHeader">
			<img src={{url("image/LOGO.png")}} id="wx_image">
			<p id="wx_nickname">红领巾商城</p>
		</div>
		<div id="homeContent">
		    <div class="orderContent">
		        <div id="waitOfferContent">
		            <ul class="orderContainer">
		            </ul>
		        </div>
		        <div id="orderContent">
		            <ul class="orderContainer">
						<li class="order">
							<table>
								<tr class="orderHeader">
									<td>
										<img src={{url("image/orderMark.png")}}>
										<span class="country">澳大利亚</span>
									</td>
									<td colspan="2">
										<small>商品号</small>
										<small class="order_id">YeYe00001</small>
									</td>
								</tr>
								<tr class="orderBody">
									<td class="imgContainer">
										<img src={{url('/image/test/mianmo.webp')}} class="requirement_image">
									</td>
									<td class="orderTitleContainer">
										<div class="orderTitleOuter">
											<p class="orderTitle">克莱氏水漾倍润保湿面膜6片补水嫩白锁水胶原蛋白</p>
											<p>
												<i><span class="totalNumber"></span></i>
												<span class="orderPriceOuter">价格：￥<span class="orderPrice">173.00</span></span>
											</p>
										</div>
									</td>
								</tr>
								<tr class="orderFooter">
									<td colspan="2">
										<span class="payOrder">购买</span>
									</td>
								</tr>
							</table>
						</li>
						<li class="order">
							<table>
								<tr class="orderHeader">
									<td>
										<img src={{url("image/orderMark.png")}}>
										<span class="country">日本</span>
									</td>
									<td colspan="2">
										<small>商品号</small>
										<small class="order_id">YeYe00002</small>
									</td>
								</tr>
								<tr class="orderBody">
									<td class="imgContainer">
										<img src={{url('/image/test/yanshuang.webp')}} class="requirement_image">
									</td>
									<td class="orderTitleContainer">
										<div class="orderTitleOuter">
											<p class="orderTitle">薇姿活性塑颜肌源焕活紧实眼霜15ml 紧致保湿紧致细纹黑眼圈</p>
											<p>
												<i><span class="totalNumber"></span></i>
												<span class="orderPriceOuter">价格：￥<span class="orderPrice">248.00</span></span>
											</p>
										</div>
									</td>
								</tr>
								<tr class="orderFooter">
									<td colspan="2">
										<span class="payOrder">购买</span>
									</td>
								</tr>
							</table>
						</li>
						<li class="order">
							<table>
								<tr class="orderHeader">
									<td>
										<img src={{url("image/orderMark.png")}}>
										<span class="country">法国</span>
									</td>
									<td colspan="2">
										<small>商品号</small>
										<small class="order_id">YeYe00003</small>
									</td>
								</tr>
								<tr class="orderBody">
									<td class="imgContainer">
										<img src={{url('/image/test/beibao.webp')}} class="requirement_image">
									</td>
									<td class="orderTitleContainer">
										<div class="orderTitleOuter">
											<p class="orderTitle">Gucci disco soho bag 小号牛皮单肩斜挎包</p>
											<p>
												<i><span class="totalNumber"></span></i>
												<span class="orderPriceOuter">价格：￥<span class="orderPrice">5690.00</span></span>
											</p>
										</div>
									</td>
								</tr>
								<tr class="orderFooter">
									<td colspan="2">
										<span class="payOrder">购买</span>
									</td>
								</tr>
							</table>
						</li>
						<li class="order">
							<table>
								<tr class="orderHeader">
									<td>
										<img src={{url("image/orderMark.png")}}>
										<span class="country">奥地利</span>
									</td>
									<td colspan="2">
										<small>商品号</small>
										<small class="order_id">YeYe00004</small>
									</td>
								</tr>
								<tr class="orderBody">
									<td class="imgContainer">
										<img src={{url('/image/test/xianglian.webp')}} class="requirement_image">
									</td>
									<td class="orderTitleContainer">
										<div class="orderTitleOuter">
											<p class="orderTitle">施华洛世奇 Swarovski Treasure Heart Bow Mini 鍊坠</p>
											<p>
												<i><span class="totalNumber"></span></i>
												<span class="orderPriceOuter">价格：￥<span class="orderPrice">1100.00</span></span>
											</p>
										</div>
									</td>
								</tr>
								<tr class="orderFooter">
									<td colspan="2">
										<span class="payOrder">购买</span>
									</td>
								</tr>
							</table>
						</li>													
		            </ul>
		        </div>
		    </div>
		</div>
	</div>

	<script type="text/javascript" src="/js/zepto.min.js"></script>
	<script type="text/javascript">
		$("#homeHeader").height(document.body.scrollWidth*0.376);
		$("#wx_image").height(document.body.scrollWidth*0.2);		
	</script>

</body>
</html>