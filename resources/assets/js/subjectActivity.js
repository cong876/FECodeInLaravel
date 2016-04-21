window.addEventListener( "load", function() {
    FastClick.attach( document.body );
}, false );

$(document).ready(function(){
	
	$(".activityImage").height($(".activityImage").width()/2);

	var placeholder;                                     //输入区域的默认文字处理
	$("body").on("focus","input,textarea",function(){
		placeholder=$(this).attr("placeholder");
		$(this).attr({"placeholder":""});
	});
	$("body").on("blur","input,textarea",function(){
		$(this).attr({"placeholder":placeholder});
	});

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

	var showTodyActivity = function (activity) {
		$("#countDownNext").hide();
		$("#countDownIndex").show();
		$(".activityImage").attr({"src":activity.activityImage});
		for(var i=$("#content").find(".item").length; i<activity.items.length; i++){
			$("#content").append($("#content").find(".item").eq(0).clone());
		};
		for(var i=$("#content").find(".item").length; i>activity.items.length; i--){
			$("#content").find(".item").last().remove();
		};
		for(var i=0; i<activity.items.length; i++){
			var indexItem = $("#content").find(".item").eq(i);
			indexItem.data("id",activity.items[i].id);
			indexItem.find(".orderNumber").text(i+1);
			indexItem.find(".itemTitle").text(activity.items[i].title);
			indexItem.find(".itemDescriptionContainer").text(activity.items[i].description);
			indexItem.find(".itemImage").attr({"src":activity.items[i].pic_url});
			indexItem.find(".itemPrice").text(activity.items[i].price);
			indexItem.find(".marketPrice").text(activity.items[i].marketPrice);
			indexItem.data("limitedNumber",activity.items[i].limitedCount);
		};
		$(".buyNow").show();
	};

	var submitItem = function () {
		var data = {
			item_id: $("#buyModal").data("id"),
			order_memo: $("#buyModal").find(".itemNote").val(),
			number: $("#buyModal").find(".itemNumberModal").find("span").text(),
			price: $("#buyModal").find(".itemPriceModal").text()
		};
		$("#finalData").find("input").eq(1).val(data.item_id);
		$("#finalData").find("input").eq(2).val(data.order_memo);
		$("#finalData").find("input").eq(3).val(data.number);
		$("#finalData").find("input").eq(4).val(data.price);
		$("#finalData").submit();
	};

	var showRegister = function () {
		$("#pagesix").show();
		$("#wx_image").height($("#wx_image").width());
		$("#pagesix").height($("#pagesix").height());
		history.pushState({page: 6},"","#pagesix");
	};

	var dataAll = $("#data").data("activity");
	dataAll = JSON.parse(dataAll);
	for(var i=0; i<dataAll.activity.items.length; i++){
		dataAll.activity.items[i] = JSON.parse(dataAll.activity.items[i]);
		dataAll.activity.items[i].price = parseFloat(dataAll.activity.items[i].price).toFixed(2);
		dataAll.activity.items[i].marketPrice = parseFloat(dataAll.activity.items[i].marketPrice).toFixed(2);
	};

	showTodyActivity(dataAll.activity);

	$("#content").on("click",".buyNow",function (event) {
		event.preventDefault();
		var itemIndex = $(this).parents(".item");
		forbidHandleScroll();
		$("#buyModal").show();
		$("#buyModal").find(".itemImageModal").find("img").attr({"src":itemIndex.find(".itemImage").attr("src")});
		$("#buyModal").find(".itemTitleModal").text(itemIndex.find(".itemTitle").text());
		$("#buyModal").find(".itemPriceModal").text(itemIndex.find(".itemPrice").text());
		$("#buyModal").find(".totalPrice").text(itemIndex.find(".itemPrice").text());
		$("#buyModal").find(".limitedNumber").text(itemIndex.data("limitedNumber"));
		$("#buyModal").data("id",itemIndex.data("id"));
	})

	$("#buyModal").on("click",".close",function (event) {
		event.preventDefault();
		allowHandleScroll();
		$("#buyModal").hide();
		$("#buyModal").find(".itemNumberModal").find("span").text(1);
		$("#buyModal").find(".limitedMark").css({opacity:"0"});
		$("#buyModal").find(".itemNote").val("");
	})

	$("#buyModal").on("click",".numberSub",function (event) {
		event.preventDefault();
		event.stopImmediatePropagation();
		$("#buyModal").find(".limitedMark").css({opacity:"0"});
		if(parseInt($("#buyModal").find(".itemNumberModal").find("span").text())>1){
			$("#buyModal").find(".itemNumberModal").find("span").text(parseFloat($("#buyModal").find(".itemNumberModal").find("span").text())-1);
			$("#buyModal").find(".totalPrice").text((parseFloat($("#buyModal").find(".totalPrice").text())-parseFloat($("#buyModal").find(".itemPriceModal").text())).toFixed(2));
		};
	})

	$("#buyModal").on("click",".numberAdd",function (event) {
		event.preventDefault();
		event.stopImmediatePropagation();
		if(parseInt($("#buyModal").find(".itemNumberModal").find("span").text())<$("#buyModal").find(".limitedNumber").text()){
			$("#buyModal").find(".itemNumberModal").find("span").text(parseFloat($("#buyModal").find(".itemNumberModal").find("span").text())+1);
			$("#buyModal").find(".totalPrice").text((parseFloat($("#buyModal").find(".totalPrice").text())+parseFloat($("#buyModal").find(".itemPriceModal").text())).toFixed(2));
		}else{
			$("#buyModal").find(".limitedMark").css({opacity:"1"});
			setTimeout(function(){
				$("#buyModal").find(".limitedMark").css({opacity:"0"});
			},2000)
		};
	})

	$("#buyModal").on("click",".sureToBuy",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		if($("#buyModal").find(".limitedNumber").text()==0){
			alert("您不能再团购该物品了！");
			return false;
		};
		$("#loading").show();
		$.ajax({                                 //验证用户是否注册过
			url:"/buyPal/checkPhone",
			type:"get",
			dataType:"json",
			data:"wx_openId",
        	beforeSend: function (xhr) {
            	var token = $("meta[name=csrf-token]").attr('content');
            	if (token) {
                	return xhr.setRequestHeader('X-CSRF-TOKEN', token);
            	}
        	},		    
			success:function(response){
				if(response.state){                 //提交商品
					submitItem();
				}else{                     //弹起注册
					$("#loading").hide();
					showRegister();
				}
	        },
	        error:function(request,errorType,errorMessage){
	        	$("#loading").hide();
	            alert("error:"+errorType+";  message:"+errorMessage);
	        }
		});
	})

	$("#pagesix").on("click",".clear",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).prev("inpu").val("");
		$(this).prev("input").focus();
	})

	var patt=/^[0-9]{11}$/;
	var mobileNumber;
	var intR;
	$("#sendcaptchas").click(function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var data={
			mobile:$("#mobile").val()
		}
		if(!patt.test(data.mobile)){
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
								intR=setInterval(
									function(){
										time=time-1;
										$("#countDown").text("重发"+"("+time+"s)");
										if(time==0){
											window.clearInterval(intR);
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

	var emailepatt=/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
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
		if(!emailepatt.test(userinfo.email)){
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
						window.clearInterval(intR);								//验证成功
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
					alert("验证失败，请重试");                        //验证失败
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
	
	$("#loading").css({backgroundColor:"rgba(255,255,255,0)"});
	$("#loading").hide();

})