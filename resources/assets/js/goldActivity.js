window.addEventListener( "load", function() {
    FastClick.attach( document.body );
}, false );

$(function(){

	var forbidden = function(e){												//禁止手机用户滚动页面
		e.preventDefault();
		e.stopPropagation();
	}

	var forbidHandleScroll = function(){										//禁止手机用户滚动页面
		document.addEventListener("touchmove",forbidden,false);
	}

	var allowHandleScroll = function(){											//解除禁止
		document.removeEventListener("touchmove",forbidden,false);
	}

	var showModal = function(target){											//弹窗
		showHide(target);
		forbidHandleScroll();
	}

	var closeModal = function(target){											//关闭弹窗
		showHide(0,target);
		allowHandleScroll();
	}

	var catchAttention = function(needBorder){                        			//未填信息指出
		needBorder.css({borderColor:"rgba(255,0,0,1)"});
		setTimeout(function(){
			needBorder.css({borderColor:"rgba(255,255,255,0)"})
		},2000);
	}

	var showHide = function(){                                					//页面切换
		var test=arguments;
		for(var i=0; i<test.length; i++){										//hide页面，每次只展示一个页面
			if(i>0){
				test[i].hide();
				test[i].css({"opacity":"0"});
			}
		};
		if(test[0]){															//第一个参数为0时，表示只hide；
			test[0].show();
			test[0].css({"opacity":"1"});
		}
	}

    var subStrByByte = function (str, limit) {									//按字节数来截取字符串
        var newStr="";
        var len=0;
        for(var i=0; i<str.length; i++){
            if((/[^\x00-\xff]/g).test(str[i])){
                len +=2;
            }else{
                len +=1;
            };
            if(len>limit){
                newStr=str.substr(0,i)+"...";
                return newStr;
            };
        };
        return str;
    }

	var saveAddress = function(newAddress,address){								//保存地址，作为后台储存成功的回调，在地址列表页和teGet弹窗页增加保存的地址
		newAddress.data("address",address);
		newAddress.find(".receiver").text(address.receiver);
		newAddress.find(".receiver_mobile").text(address.receiver_mobile);
		newAddress.find(".receiver_address").text(address.receiver_address);
		$("#receiving_addresses").find(".editAddressesContent").append(newAddress);
		showAddress(address);
		newAddress.show();
		if(address.setDefault==1){
			setDefaultAddress(newAddress);
		};
		$("#editUserAddress").find("input").val("");
		$("#editUserAddress").find("select").eq(0).find("option").eq(0).attr({"selected":"selected"});
		$("#editUserAddress").find("select").eq(1).html("");
		$("#editUserAddress").find("select").eq(2).html("");
		forbidHandleScroll();
	}

	var showAddress = function(address){										//在兑换页展示地址
		$("#toGet").data("address-id",address.id);
		$("#toGet").find(".receiver").text(address.receiver);
		$("#toGet").find(".receiver_mobile").text(address.receiver_mobile);
		$("#toGet").find(".receiver_address").text(address.receiver_address);
	}

	var setDefaultAddress = function(addressIndex){                     		//设置默认地址
		for(var i=0; i<$(".editAddressesContent").find(".default").length; i++){
			$(".editAddressesContent").find(".default").eq(i).text("");
		};
		$(".editAddressesContent").find(".checked").removeClass("checked");
		addressIndex.addClass("checked");
		addressIndex.find(".default").text("[默认]");
	}

	var showRegister = function () {											//拉起注册页
		showHide($("#pagesix"),$("#main"));
		$("#wx_image").height($("#wx_image").width());
		$("#pagesix").height($("#pagesix").height());
		history.pushState({page: 6},"","#pagesix");
	};

	var submitItem = function(){												//提交兑换商品
		showHide($("#loading"));
		var dataContainer = $("#finalData").find("input");
		var item = $("#toGet").data("item");
		dataContainer.eq(1).val(item.id);										//商品id
		dataContainer.eq(2).val($("#toGet").find(".itemNumber").text());		//商品数量
		dataContainer.eq(3).val(item.coins);									//商品价格
		dataContainer.eq(4).val($("#toGet").data("address-id"));				//用户地址id
		$("#finalData").submit();
	}

	var getMoreFriends = function(){
		$.ajax({
            url:"getMoreSupporters?page="+($("#footer").find(".friendsContainer").find("div").length/10+1),
            type:"get",
            dataType:"json",
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                };
            },
            success: function (response) {
            	console.log(response);
            	response.supporters.forEach(function(supporter){
					$("#footer").find(".friendsContainer").append(
						"<div class='imageContainer'><img src='"+supporter.support_headImgUrl+"'></div>");
            	});
            	showHide($("#footer").find(".checkMore"),$("#smallLoading"));
            	showNomore(response.supporters.length);
            },
            error: function (request, errorType, errorMessage) {
                alert("加载失败，请重试？");
                showHide(0,$("#loading"));
            }
		})
	}

	var showNomore = function(count){											//当前朋友数小于10时隐藏加载按钮
		if(count<10) showHide(0,$("#footer").find(".checkMore"));
	}

	var starActivity = {                                           				//全局变量打包
		mobilePatt:/^[0-9]{11}$/,
		emailPatt:/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/,
		mobile:0,
		placeholder:"",															//input,textarea提示文字聚焦时消失处理
		hasRegisted:0,															//用户是否注册
		hasAddress:0															//用户是否有收货地址
	}

	var items = $("#data").data("init").items.map(function(item){				//数据初始化
			item = JSON.parse(item);
			item.inventory=parseInt(item.limitedCount);
			item.pic_url = JSON.parse(item.pic_url);
			item.coins=item.coins-0;
			item.marketPrice=item.marketPrice-0;
			return item
		}),
		addresses = $("#data").data("init").address_info.map(function(address){
			address.id=address.address_id;
			address.receiver=address.receiver_name;
			address.receiver_address=subStrByByte(address.receiver_address,62);
			return address
		}),
		user = {
			stars: $("#data").data("init").user.coins-0,
			friends: $("#data").data("init").supporters,
			state: JSON.parse($("#data").data("init").register_state)
		};
		console.log(items,addresses,user);

	if(user.state){
		starActivity.hasRegisted = 1;
	}
	$("#header").find(".starsNumber").text(user.stars);

	items.forEach(function(item){												//初始化数据展示
		var itemShow = $("#content").find(".itemEx").eq(0).clone();
		itemShow.find(".itemImage").attr({"src":item.pic_url});
		itemShow.find(".itemTitle").text(item.title);
		itemShow.find(".itemDescription").text(item.description);
		itemShow.find(".price").text(item.coins);
		itemShow.find(".marketPrice").text(parseFloat(item.marketPrice).toFixed(2));
		itemShow.find(".getFree").data("item",item);
		if(item.coins>user.stars) itemShow.find(".getFree").attr({"class":"toAddStar"}).text("我想要");
		itemShow.attr({"class":"item"});
		$("#content").find(".itemEx").eq(0).before(itemShow);
	})

	if(addresses.length>=1){
		showAddress(addresses[0]);
		starActivity.hasAddress=1;
	}else{
		$("#toGet").find(".addressContainer").find("p").eq(0).css({opacity:"0"});
		starActivity.hasAddress=0;
	}

	addresses.forEach(function(address){
		var addressShow = $("#receiving_addresses").find(".user_AddressEx").eq(0).clone();
		addressShow.find(".receiver").text(address.receiver);
		addressShow.find(".receiver_mobile").text(address.receiver_mobile);
		addressShow.find(".receiver_address").text(address.receiver_address);
		addressShow.data("address",address);
		addressShow.attr({"class":"user_Address"});
		if(address.isDefault==1){
			setDefaultAddress(addressShow);
			showAddress(address);
		};
		$("#receiving_addresses").find(".user_AddressEx").eq(0).before(addressShow);
	})

	user.friends.forEach(function(friend){
		$("#footer").find(".friendsContainer").append("<div class='imageContainer'><img src='"+friend.support_headImgUrl+"'></div>");
	})

	showNomore(user.friends.length);

	window.onpopstate = function(){												//回退事件处理
		var targetHash = location.hash;
		switch(targetHash){
			case "#main":
				showHide($("#main"),$("#editUserAddress"),$("#receiving_addresses"),$("#pagesix"));
				break;
		}
	}

	history.replaceState("","","#main");

	$("body").on("focus","input,textarea",function(){       					//输入区域的默认文字处理
		starActivity.placeholder=$(this).attr("placeholder");
		$(this).attr({"placeholder":""})
	})
	$("body").on("blur","input,textarea",function(){
		$(this).attr({"placeholder":starActivity.placeholder})
	})

	// $("#header").on("click",".toFriends",function(event){						//点击平缓滚动到页面底部
	// 	event.preventDefault();
	// 	var maxScroll = document.body.scrollHeight - $(window).height();
	// 	var int = setInterval(function(){
	// 		window.scrollBy(0,50);
	// 		if(document.body.scrollTop>=maxScroll){
	// 			clearInterval(int);
	// 		};
	// 	},10);
	// });

	$("#content").on("click",".toAddStar",function(event){						//拉起指示转发页面（有星星不足提示）
		event.preventDefault();
		showModal($("#toShare"));
		$("#toShare").find(".fromIwanna").show();
	})

	$("#header").on("click",".addStar",function(event){							//拉起指示转发页面
		event.preventDefault();
		showModal($("#toShare"));
	})

	$("#content").on("click",".getFree",function(event){
		event.preventDefault();
		var itemIndex = $(this).data("item");
		if(itemIndex.inventory==0) $("#toGet").find(".itemNumber").text(0);
		$("#toGet").find(".itemImage").attr({"src":itemIndex.pic_url});
		$("#toGet").find(".itemTitleOuter").text(itemIndex.title);
		$("#toGet").find(".itemStars").text(itemIndex.coins);
		$("#toGet").data("item",itemIndex);
		showModal($("#toGet"));
	})

	$(".close").on("click",function(event){
		event.preventDefault();
		if($(this).parents(".modal").is("#toShare")){
			$("#toShare").find(".fromIwanna").hide();
		}else{
			$(this).parents(".modal").find(".itemNumber").text(1);
		};
		closeModal($(this).parents(".modal"));
	})

	$("#footer").on("click",".checkMore",function(event){
		event.preventDefault();
		var that = this;
		showHide($("#smallLoading"),$(that));
		getMoreFriends();
	})

	$("#toGet").on("click",".addressContainer",function(event){					//点击兑换页地址区域进行的操作
		event.preventDefault();
		allowHandleScroll();
		if(starActivity.hasAddress == 0){										//没有地址时，直接跳到编辑地址页
			showHide($("#editUserAddress"),$("#main"));
			history.pushState("","","#addAddress");
			$("#editUserAddress").height($("#editUserAddress").height());		//防止拉起键盘时高度改变
		}else{
			showHide($("#receiving_addresses"),$("#main"));
			history.pushState("","","#addressesList");
		}
	})

	$("#toGet").on("click",".addNumber",function(event){						//增加商品数量
		event.preventDefault();
		var num = $("#toGet").find(".itemNumber");
		var stars = $("#toGet").find(".itemStars");
		if(num.text()==$("#toGet").data("item").inventory){
			$("#toGet").find(".limitedExplain").css({opacity:"1"}).text("库存不足，红领巾正在补货");
			setTimeout(function(){$("#toGet").find(".limitedExplain").css({opacity:"0"})},1000);
			return false;
		}else if($("#toGet").data("item").coins*(num.text()-0+1)>user.stars){
			$("#toGet").find(".limitedExplain").css({opacity:"1"}).text("您的小星星不能兑换更多了");
			setTimeout(function(){$("#toGet").find(".limitedExplain").css({opacity:"0"})},1000);
			return false;
		};
		num.text(num.text()-0+1);
		stars.text($("#toGet").data("item").coins*(num.text()-0));
	})

	$("#toGet").on("click",".subNumber",function(event){						//减少商品数量
		event.preventDefault();
		var num = $("#toGet").find(".itemNumber");
		var stars = $("#toGet").find(".itemStars");
		if(num.text()>1) num.text(num.text()-1), stars.text($("#toGet").data("item").coins*(num.text()-0));
	})

	$("#toGet").on("click",".getFree",function(event){
		event.preventDefault();
		if(!$("#toGet").data("address-id")){
			alert("请添加收货地址");
			return false;
		};
		if($("#toGet").find(".itemNumber").text()==0){
			$("#toGet").find(".limitedExplain").css({opacity:"1"}).text("库存不足，红领巾正在补货");
			setTimeout(function(){$("#toGet").find(".limitedExplain").css({opacity:"0"})},1000);
			return false;
		};
		if(starActivity.hasRegisted == 0){
			showRegister();
		}else{
			submitItem();
		};
	})


// 地址管理
	$("#receiving_addresses").on("click",".addNewAddress",function(event){
		event.preventDefault();
		showHide($("#editUserAddress"),$("#receiving_addresses"));
	})

	$("#receiving_addresses").on("click",".user_Address",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		showAddress($(this).data("address"));
		history.back();
		forbidHandleScroll();
	})

	$("#province").on("change",function(event){                					//选择省加载市
		event.preventDefault();
		event.stopImmediatePropagation();
		var data=$(this).val();
		if(data==0){
			return false;
		}else if(data==710000){													//香港、澳门、台湾等信息处理
            $("#city").html("<option value='1'>--不用选啦--</option>");
            $("#county").html("<option value='1'>--不用选啦--</option>");
			$("#receiver_zipCode").val("000822");
			return false;
		}else if(data==810000){
            $("#city").html("<option value='1'>--不用选啦--</option>");
            $("#county").html("<option value='1'>--不用选啦--</option>");
			$("#receiver_zipCode").val("999077");
			return false;
		}else if(data==820000){
            $("#city").html("<option value='1'>--不用选啦--</option>");
            $("#county").html("<option value='1'>--不用选啦--</option>");
			$("#receiver_zipCode").val("000853");
			return false;
		}
        $("#county").html("");
        $("#receiver_zipCode").val("");
        $.ajax({
            url:"/user/getSubRegion/"+data,
            type:"get",
            dataType:"html",
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                };
            },
            success: function (response) {
            	$("#city").html("<option value='0'>--请选择--</option>"+response);
            },
            error: function (request, errorType, errorMessage) {
                alert("error:" + errorType + ";  message:" + errorMessage);
            }
        })
	})

	$("#city").on("change",function(event){                						//选择市加载县
		event.preventDefault();
		event.stopImmediatePropagation();
		var data=$(this).val();
		if(data==0){
			return false;
		}
        $("#receiver_zipCode").val("");
        $.ajax({
            url:"/user/getSubRegion/"+data,
            type:"get",
            dataType:"html",
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                };
            },
            success: function (response) {
            	if(response==""){
            		$("#county").html("<option value='1'>--不用选啦--</option>");
            	}else{
            		$("#county").html("<option value='0'>--请选择--</option>"+response);
            	}
            },
            error: function (request, errorType, errorMessage) {
                alert("error:" + errorType + ";  message:" + errorMessage);
            }
        })
	})

	$("#county").on("change",function(event){         							//选择县添加邮编
		event.preventDefault();
		event.stopImmediatePropagation();
		var data=$(this).val();
		if(data==0){
			return false;
		};
		if($(this).find("option").eq(0).text()=="--不用选啦--"){
			return false;
		};
		$("#receiver_zipCode").val("");
        $.ajax({
            url:"/user/getZipCode/"+data,
            type:"get",
            dataType:"json",
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                };
            },
            success: function (response) {
            	$("#receiver_zipCode").val(response);
            },
            error: function (request, errorType, errorMessage) {
                alert("error:" + errorType + ";  message:" + errorMessage);
            }
        })
	})

	$("#editUserAddress").on("click",".setAddressContainer",function(event){   	//选取为默认地址
		event.preventDefault();
		event.stopImmediatePropagation();
		if($("#setDefaultAddress").hasClass("icon-radio-checked")){
			$("#setDefaultAddress").attr({"class":"icon-radio-unchecked"})
		}else{
			$("#setDefaultAddress").attr({"class":"icon-radio-checked"})
		}
	})

	$("#saveAddress").on("click",function(event){    							//保存地址
		event.preventDefault();
		event.stopImmediatePropagation();
		if($("#receiver").val()==""){
			catchAttention($("#receiver"));
			return false;
		};
		if(!starActivity.mobilePatt.test($("#receiver_mobile").val())){
			catchAttention($("#receiver_mobile"));
			alert("请输入正确的手机号码");
			return false;
		};
		for(var i=0; i<$("#editUserAddress").find("select").length; i++){
			if($("#editUserAddress").find("select").eq(i).val()==0){
				catchAttention($("#editUserAddress").find("select").eq(i));
				return false;
			}
		};
		if($("#receiver_address").val()==""){
			catchAttention($("#receiver_address"));
			return false;
		};
		if($("#receiver_zipCode").val()==""){
			catchAttention($("#receiver_zipCode"));
			return false;
		};
		var address={                                                  			//传给后台的数据
			receiver:$("#receiver").val(),
			receiver_mobile:$("#receiver_mobile").val(),
			province:$("#province").val(),
			city:$("#city").val(),
			county:$("#county").val(),
			receiver_zip_code:$("#receiver_zipCode").val(),
			receiver_address:$("#receiver_address").val(),
			setDefault:0
		};
		if($("#setDefaultAddress").hasClass("icon-radio-checked")){
			address.setDefault=1;
		};
		showHide($("#loading"));
		$.ajax({
            url:"/user/createReceivingAddress",
            type:"post",
            dataType:"json",
            data:address,
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                };
            },
            success: function (response) {
            	if(response>0){
            		address.id = response;
					var addressText = $("[value='"+address.province+"']").text()+($("[value='"+address.city+"']").text()=="--不用选啦--" ? "" : $("[value='"+address.city+"']").text())+($("[value='"+address.county+"']").text()=="--不用选啦--" ? "" : $("[value='"+address.county+"']").text())+address.receiver_address;
					var addressDetail = subStrByByte(addressText,62);
        			address.receiver_address = addressDetail;
        			var newAddress = $("#receiving_addresses").find(".user_AddressEx").eq(0);
        			newAddress.attr({"class":"user_Address"});
            		if(starActivity.hasAddress==0){
            			$("#toGet").find(".addressContainer").find("p").eq(0).css({opacity:"1"});
            			saveAddress(newAddress,address);
            			starActivity.hasAddress=1;
            		}else{
            			saveAddress(newAddress,address);
            		};
            		history.back();
            		forbidHandleScroll();
            		showHide(0,$("#loading"));
            	}
            },
            error: function (request, errorType, errorMessage) {
                alert("地址保存失败，请重试？");
                showHide(0,$("#loading"));
            }
		})
	})



// 注册页面
	$("#pagesix").on("click",".clear",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).prev("inpu").val("");
		$(this).prev("input").focus();
	})

	var mobileNumber;
	var intR;
	$("#sendcaptchas").click(function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var data={
			mobile:$("#mobile").val()
		}
		if(!starActivity.mobilePatt.test(data.mobile)){
			alert("请输入11位中国大陆手机号码");
			return false
		};
		var that=this;
		var time=50;
		$("#loading").show();
		$.ajax({
			url:"/buyPal/authMobile/"+data.mobile,                             //验证用户手机是否注册过的url
			type:"get",
			dataType:"json",
	        beforeSend: function (xhr) {
	            var token = $("meta[name=csrf-token]").attr('content');
	            if (token) {
	                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	            }
	        },
			success:function(response){
            	if(response==0){
            		$("#loading").hide();
            		alert("手机号码已被注册，请更换号码或联系客服");
            	}else{
            		$.ajax({
            			url: "/buyPal/getVerifyCode/"+data.mobile,
            			type: "get",
            			dataType: "json",
            			beforeSend: function (xhr) {
            				var token = $("meta[name=csrf-token]").attr('content');
            				if (token) {
            					return xhr.setRequestHeader('X-CRSF-TOKEN', token);
            				}
            			},
            			success: function(res){
            				$("#loading").hide();
            				if(res.status){
								$(that).hide();
								codeState=0;
								$("#countDown").attr({"style":"display:inline-block"});
								$("#countDown").text("重发"+"("+50+"s)");
								mobileNumber=data.mobile;
								$("#loading").hide();
								int=setInterval(
									function(){
										time=time-1;
										$("#countDown").text("重发"+"("+time+"s)");
										if(time==0){
											window.clearInterval(int);
											$("#countDown").hide();
											$("#sendcaptchas").show();
										}
								},1000);         //发送成功
            				}else{
            					alert("发送失败，稍后请重试");
            				}
            			},
			        	error:function(request,errorType,errorMessage){
			        		$("#loading").hide();
			            	alert("error:"+errorType+";  message:"+errorMessage);//发送失败
            			}
            		})
            	}
        	},
        	error:function(request,errorType,errorMessage){
        		$("#loading").hide();
            	alert("error:"+errorType+";  message:"+errorMessage);
        	}
		})
	})

	var mobileStatus;

	$("#pagesix").on("click","#register",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var userinfo={
			mobile:"",
			email:""
		}
		userinfo.mobile=mobileNumber;
		userinfo.email=$("#email").val();
		if(!mobileNumber){
			alert("请输入11位中国大陆手机号码并验证");
			return false;
		};
		if(!starActivity.emailPatt.test(userinfo.email)){
			alert("邮箱格式不正确，请重新输入");
			return false;
		};
		if($("#captchas").val()==""){
			alert("请输入验证码");
			return false;
		};
		if(codeState==0){
			$("#loading").show();
			$.ajax({
				url: "/buyPal/verifyCode/"+mobileNumber+"/"+$("#captchas").val(),
				type: "get",
				dataType: "json",
		        beforeSend: function (xhr) {
		            var token = $("meta[name=csrf-token]").attr('content');
		            if (token) {
		                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
		            }
		        },
		        success: function(res){
		        	if(res.status){
						window.clearInterval(int);								//验证成功
						codeState=1;
						$.ajax({             
							url:"/buyPal/register",
							type:"post",
							dataType:"json",
							data:userinfo,
					        beforeSend: function (xhr) {
					            var token = $("meta[name=csrf-token]").attr('content');
					            if (token) {
					                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
					            }
					        },
							success:function(response){
								if(response==1){
									submitItem();
								}else{
									$("#loading").hide();
									alert("邮箱已被注册，请更换邮箱")
								}
				        	},
				        	error:function(request,errorType,errorMessage){
				        		$("#loading").hide();
				            	alert("error:"+errorType+";  message:"+errorMessage);
				        	}
						})
					}else{
						$("#loading").hide();
						alert("验证码错误，请重新输入");
					}
		        },
		        error: function(err){
					$("#loading").hide();
					alert("验证失败，请重试");                        			//验证失败
		        }
			})
		}else{
			$.ajax({
				url:"/buyPal/register",
				type:"post",
				dataType:"json",
				data:userinfo,
		        beforeSend: function (xhr) {
		            var token = $("meta[name=csrf-token]").attr('content');
		            if (token) {
		                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
		            }
		        },
				success:function(response){
					if(response==1){
						submitItem();
					}else{
						$("#loading").hide();
						alert("邮箱已被注册，请重新填写")
					}
	        	},
	        	error:function(request,errorType,errorMessage){
	        		$("#loading").hide();
	            	alert("error:"+errorType+";  message:"+errorMessage);
	        	}			
			})
		}
	})

	$("#footer").find(".friendsContainer").find("img").height($("#footer").find(".friendsContainer").find("img").width()); //头像显示为正圆

	$("#loading").hide();
	$("#loading").css({"backgroundColor":"rgba(255,255,255,0)"});


});