<?php

$regionInstance = \App\Helper\ChinaRegionsHelper::getInstance();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta content="email=no" name="format-detection" />
	<title>个人中心</title>
	<link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href={{url("css/myOrder.min.css")}}>
	<div id='wx_pic' style='margin:0 auto;display:none;'>
		<img src={{url("image/LOGO.jpg")}} />
	</div>
</head>
<body>
	<?php
	$hlj_id = Auth::user()->hlj_id;
	$user = DB::table('users')->where('hlj_id',$hlj_id)->first()    //有登陆认证页面后渲染登陆用户
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
			<img src={{!empty($user->headimgurl) ? $user->headimgurl : url("image/OtherArea.jpg")}} id="wx_image">
			<p id="wx_nickname">{{$user->nickname}}</p>
		</div>
		<div id="homeTabs">
			<ul class="tabContainer">
				<li class="homeTab" id="waitOffer">
					<span class="icon-iconfont-waitOffer icon"></span><br/>
					<span class="tabStatus">待报价</span></li>
				<li class="homeTab" id="waitPay">
					<span class="icon-iconfont-waitPay icon"><span class="catchAttention" style="letter-spacing: -1px;white-space: nowrap;"></span></span><br/>
					<span class="tabStatus">待付款</span>
				</li>
				<li class="homeTab" id="waitSend">
					<span class="icon-iconfont-waitSend icon"></span><br/>
					<span class="tabStatus">待发货</span>
				</li>
				<li class="homeTab" id="sent">
					<span class="icon-iconfont-sent icon"></span><br/>
					<span class="tabStatus">已发货</span>
				</li>
				<li class="homeTab" id="completed">
					<span class="icon-iconfont-completed icon"></span><br/>
					<span class="tabStatus">已完成</span>
				</li>
				<hr class="tabFooter">
			</ul>		
		</div>
		<div id="homeTabsFixed">
			<ul class="tabContainer">
				<li class="homeTab waitOffer">
					<span class="icon-iconfont-waitOffer icon"></span><br/>
					<span class="tabStatus">待报价</span>
				</li>
				<li class="homeTab waitPay">
					<span class="icon-iconfont-waitPay icon"><span class="catchAttention" style="letter-spacing: -1px;white-space: nowrap;"></span></span><br/>
					<span class="tabStatus">待付款</span>
				</li>
				<li class="homeTab waitSend">
					<span class="icon-iconfont-waitSend icon"></span><br/>
					<span class="tabStatus">待发货</span>
				</li>
				<li class="homeTab sent">
					<span class="icon-iconfont-sent icon"></span><br/>
					<span class="tabStatus">已发货</span>
				</li>
				<li class="homeTab completed">
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
		        <div id="waitOfferContent">
		            <ul class="orderContainer">
		            </ul>
		        </div>
		        <div id="orderContent">
		            <ul class="orderContainer">
		            </ul>
		        </div>
		    </div>
		</div>
	</div>

<!-- 弹窗页 -->
	<div id="cancleOrder">
		<table>
			<tr>
				<td>
					<p>是否取消该订单？</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<span class="keepItOn">否</span>
						<span class="sureToCancle" data-order-id data-order-index>是</span>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<div id="callOp">
		<table>
			<tr>
				<td>
					<p>拨打客服电话：<span class="opTel"></span></p>
					<p>(客服工作时间：9:00-18:00)</p>
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

	<div id="checkRefund">
		<table>
			<tr>
				<td class="refund_detailOuter">
					<p class="refund_detail">
						<span>退款商品：</span><span class="refund_title"></span><br/>
						<span>退款数量：</span><span class="refund_number"></span><br/>
						<span>退款金额：</span><span class="refund_amount"></span><br/>
						<span>退款说明：</span><span class="refund_description"></span><br/>
						<span class="placeholder">占位</span>	
					</p>
					<p><small>退款通常5个工作日内到账</small></p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<span class="keepItOn">好哒</span>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<div id="toReceive">
		<table>
			<tr>
				<td>
					<p>确认已收货？</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<span class="keepItOn">取消</span>
						<span class="sureToReceive" data-order-id data-order-index>确认</span>
					</p>
				</td>
			</tr>
		</table>
	</div>

	<div id="exTime">
		<table>
			<tr>
				<td>
					<p>请在报价后72小时内付款，逾期则订单失效。</p>
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

<!-- 确认付款页 -->
	<div id="sureToPay">
		<div id="sureToPayHeader">
			<table>
				<tbody>
					<tr>
						<td>
							<div class="receiving_addresses" data-receiving-address-id="">
								<p>
									<span>收货人：</span>
									<span class="receiver_name"></span>
									<span class="receiver_mobile"></span>
								</p>
								<p>
									<span>收货地址：</span>
									<span class="receiver_address"></span>
								</p>
							</div>
						</td>
						<td>
							<span>&nbsp;></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="sureToPayContent">
			<li class="itemContainer" data-item-description="" data-item-url="" data-item-memos="">
				<table>
					<tbody>
						<tr>
							<td class="itemImageOuter">
								<img src="" class="itemImage">
							</td>
							<td class="itemTitleOuter">
								<p class="itemTitle"></p>
								<p class="itemNumberOuter"><sapn style="font-family: sans-serif">x</sapn><span class="itemNumber"></span></p>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
		</div>
		<div id="payOrderOverview">
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
		<div id="payOrderPlaceholder"></div>
		<div id="sureToPayFooter">
			<div class="sureToPay">
				<p>确认支付</p>
			</div>			
		</div>
	</div>

<!-- 确认支付页下的收货地址列表	 -->
	<div id="receiving_addresses">
		<div></div>
		<div class="editAddressesFooter">
			<div class="addNewAddress">
				<p>添加收货地址</p>
			</div>
		</div>		
	</div>

<!-- 商品列表页 -->
	<div id="itemList">
		<div id="itemListHeader">
			<table>
				<tbody>
					<tr>
						<td>
							<img class="forWaitOffer buyerImage" src={{url("image/child.png")}}>
							<img class="forWaitPay buyerImage" src={{url("image/DefaultPicture.jpg")}}>
						</td>
						<td>
							<div class="forWaitOffer">
								<p>此订单正在等待买手报价。</p>
								<p>别着急~24小时内会有反馈哒~</p>
							</div>
							<div class="forWaitPay">
								<p><span class="buyerName"></span><small style="font-weight:500">&nbsp;为您代购</small></p>
								<p class="placeholder">占位</p>
								<p><span class="icon-location">  </span><span class="buyerPosition"></span></p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="itemStatusOuter">
			<table>
				<tr>
					<td><p class="itemStatus"></p></td>
				</tr>
			</table>
		</div>
		<div id="itemListContent">
			<li class="itemContainer" data-item-title="" data-item-description="" data-item-url="" data-item-memos="" data-item-id="">
				<table>
					<tbody>
						<tr>
							<td class="itemImageOuter">
								<img src="" class="itemImage">
							</td>
							<td class="itemTitleOuter">
								<p class="itemTitle"></p>
								<p class="itemNumberOuter"><span class="hasRefund" style="display: inline-block; float: left">已退款</span><span style="font-family: sans-serif">x</span><span class="itemNumber"></span></p>
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
				<p class="submitTimeOuter"><span>提交时间：</span><span class="submitTime"></span></p>
				<p class="offerTimeOuter"><span>报价时间：</span><span class="offerTime"></span></p>
				<p class="exTimeOuter"><span>有效时间：</span><span class="exTimeInner"></span><span class="exTime">&nbsp;<a>有效时间</a></span></p>
				<p class="payTimeOuter"><span>付款时间：</span><span class="payTime"></span></p>
				<p class="sendTimeOuter"><span>发货时间：</span><span class="sendTime"></span></p>
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
			{{--<p class="descriptionTitle">客服备注</p>--}}
			{{--<p class="opNote"></p>--}}
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
							<td><sapn>头像</sapn></td>
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
			<li class="user_addressesContainer">
				<table>
					<tbody>
						<tr>
							<td><span>收货地址管理</span></td>
							<td>
								<span class="user_addresses"></span>
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
							<td><p>邮箱</p></td>
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
		<div id="user_addresses">
			<div class="editAddressesContent">
				<li class="user_AddressEx" style="display:none" data-receiver="" data-receiver-mobile="" data-receiver-area="" data-city="" data-city-code="" data-county="" data-county-code="" data-receiver-address="">
					<table>
						<tbody>
							<tr>
								<td><span class="default"></span><span class="receiver"></span></td>
								<td><span class="receiver_mobile"></span></td>
								<td rowspan="2" class="deleteOut">
									<span class="icon-bin"></span>
									<span class="deleteAddress">删除</span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span class="receiver_address"></span>
								</td>
							</tr>
						</tbody>
					</table>					
				</li>

				<?php
				$hlj_id = Auth::user()->hlj_id;
				//$addresses = DB::table('receiving_addresses')->where('hlj_id',$hlj_id)->where('is_available',true)->get();
				$addresses = App\Models\ReceivingAddress::where('hlj_id',$hlj_id)->where('is_available',true)->get()->sortByDesc('is_default');
				?>
				@foreach($addresses as $address)
					<?php
						//$address_get = $address;
					$secondAreaName = '';
					$secondAreaCode = '';
					$thirdAreaName = '';
					$thirdAreaCode = '';
					$firstArea = $regionInstance->getRegionByCode($address->first_class_area);
					if(($address->first_class_area == 710000)||($address->first_class_area == 810000)||($address->first_class_area == 820000))
					{
						$secondAreaName .= '--不用选啦--' . ',';
						$secondAreaCode .= '--不用选啦--' . ',';
						$thirdAreaName .= '--不用选啦--' . ',';
						$thirdAreaCode .= '--不用选啦--' . ',';
						$secondName=" ";
						$thirdName=" ";
					}
					else{
					$secondAreas = $firstArea->childRegions;
					$secondClassArea = $regionInstance->getRegionByCode($address->second_class_area);
					$thirdAreas = $secondClassArea->childRegions;
					foreach($secondAreas as $secondArea)
					{
						$secondAreaName .= $secondArea->name . ',';
						$secondAreaCode .= $secondArea->code . ',';
					}
						if(count($thirdAreas) == 0)
						{
							$thirdAreaName .= '--不用选啦--' . ',';
							$thirdAreaCode .= '--不用选啦--' . ',';
							$thirdName=" ";
						}
						else {
					foreach($thirdAreas as $thirdArea)
					{
						$thirdAreaCode .= $thirdArea->code . ',';
						$thirdAreaName .= $thirdArea->name . ',';
					} }
					}
					$firstName=$firstArea->name;
					if($address->is_default ==1)
						{
							$default=' checked';
							$status="[默认]";
						}
					else
					{
						   $default='';
						   $status='';
					}
					$secondAreaName = substr($secondAreaName,0,-1);
					$secondAreaCode = substr($secondAreaCode,0,-1);
					$thirdAreaCode = substr($thirdAreaCode,0,-1);
					$thirdAreaName = substr($thirdAreaName,0,-1);
					if(($address->first_class_area != 710000)&&($address->first_class_area != 810000)&&($address->first_class_area != 820000))
					{
					$secondName=$secondClassArea->name;
						if($address->third_class_area == 1){
							$thirdName = " ";
						}
						else {
							$thirdName=$regionInstance->getRegionByCode($address->third_class_area)->name;
						}
					}
					$secondName = str_replace(" ",'',$secondName);
					$thirdName = str_replace(" ",'',$thirdName);
					$addressText=$firstName.$secondName.$thirdName.$address->street_address;
				    if (mb_strlen($addressText, 'utf-8') > 31) {
                        $addressText = mb_substr($addressText, 0, 31) . '...';
                    }
					?>
					<li class="user_Address{{$default}}" 
						data-receiver-address-id="{{$address->receiving_addresses_id}}" 
						data-receiver="{{$address->receiver_name}}" 
						data-receiver-mobile="{{$address->receiver_mobile}}" 
						data-receiver-area="{{$address->first_class_area}},{{$address->second_class_area}},{{$address->third_class_area}},{{$address->receiver_zip_code}}" 
						data-city="{{$secondAreaName}}"
						data-city-code="{{$secondAreaCode}}"
						data-county="{{$thirdAreaName}}"
						data-county-code="{{$thirdAreaCode}}"
						data-receiver-address="{{$address->street_address}}">
						<table>
							<tbody>
							<tr>
								<td><span class="default">{{$status}}</span><span class="receiver">{{$address->receiver_name}}</span></td>
								<td><span class="receiver_mobile">{{$address->receiver_mobile}}</span></td>
								<td rowspan="2" class="deleteOut">
									<span class="icon-bin"></span>
									<span class="deleteAddress">删除</span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span class="receiver_address">{{$addressText}}</span>
								</td>
							</tr>
							</tbody>
						</table>
					</li>
				@endforeach

			</div>
			<div class="editAddressesFooter">
				<div class="addNewAddress">
					<p>添加收货地址</p>
				</div>
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

	<script type="text/javascript" src="https://one.pingxx.com/lib/pingpp_one.js"></script>
	<script src="http://7xnzm2.com2.z0.glb.qiniucdn.com/myOrder.min.js"></script>
	<script type="text/javascript">
		if (window.location != window.parent.location) window.parent.location = window.location;
	</script>	

</body>
</html>