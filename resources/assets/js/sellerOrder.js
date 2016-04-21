	window.addEventListener( "load", function() {
	    FastClick.attach( document.body );
	}, false );

	var sellerOrder={                                           //全局变量打包
		mobilePatt:/^[0-9]\d*$/,
		codePatt:/^[0-9]\d*$/,
		emailPatt:/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/,
		alipayPatt1:/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
		alipayPatt2:/^1\d{10}$/,
		bankCardPatt:/^[0-9]\d{13,18}$/,
		paymentId:0,
		mobile:0,
		code:0,
		setDefaultIndex:0,
		placeholder:"",
		editIndex:0
	}
	var int;

	var showHide=function(){                                //页面切换
		for(var i=0; i<arguments.length; i++){
			if(i>0){
				arguments[i].hide();
				arguments[i].css({"opacity":"0"});
			}
		};
		if((arguments[0].attr("id")=="orderContent")||(arguments[0].attr("class")=="noOrderContent")||(arguments[0].attr("id")=="home")){
			arguments[0].show();
			arguments[0].animate({"opacity":"1"},300)
		}else{
			arguments[0].show();
			arguments[0].css({"opacity":"1"});
			arguments[0].height(arguments[0].height());
		};
	}

	var checkOrderNum=function(){                           //检测当前tab下订单数量
		if($("#orderContent").find(".order").length>0){
			$(".noOrderContent").css({"opacity":"0"});
			$(".noOrderContent").hide();
		}else{
			showHide($(".noOrderContent"));
		};
	}

	var catchAttention=function(needBorder){                         //未填信息指出
		needBorder.css({borderColor:"rgba(255,0,0,1)"});
		setTimeout(function(){
			needBorder.css({borderColor:"rgba(255,255,255,0)"})
		},2000);
	}

	var clearZero=function(num){
		if(num[0]==0){
			num=num.substring(1,num.length); 
			return clearZero(num)
		}else{
			return num
		}
	}

    var isChineseChar=function(str){   
       var reg = /[\u4E00-\u9FA5\uF900-\uFA2D]/;
       return reg.test(str);
    }

	var changeTab=function(that){
		var url;
		switch($(that).index()){
			case 0:
				url="toggleToReceived";
				break;
			case 1:
				url="toggleToNeedToDelivery";
				break;
			case 2:
				url="toggleToAuditing";
				break;
			case 3:
				url="toggleToHasDelivered";
				break;
			case 4:
				url="toggleToHasFinished";
				break;
		}
        $.ajax({
            url:url,
            type:"get", 
            dataType:"html",
            beforeSend: function (xhr) {
                var token = $("meta[name=csrf-token]").attr('content');
                if (token) {
                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                };
            },
            success: function (response) {
            	$("#orderContent").find(".orderContainer").html(response);
            	showHide($("#orderContent"),$("#loading"));
            	if(url=="toggleToNeedToDelivery"){
            		$(".catchAttention").text($("#orderContent").find(".orderContainer").find(".order").length);
	            	if(response==""){
	            		$(".catchAttention").css({opacity:"0"})
	            	}else{
	            		$(".catchAttention").css({opacity:"1"})
	            	}
            	};                	
            	checkOrderNum();                	
            },
            error: function (request, errorType, errorMessage) {
                alert("error:" + errorType + ";  message:" + errorMessage);
                $("#loading").hide();
            }
        })
	}

	$(document).ready(function(){

		$("#homeTabs").height(document.body.scrollWidth*0.173);

		window.onpopstate=function(event){
			event.preventDefault();
			switch(location.hash){
				case "#home":
					document.title="买手中心";
					showHide($("#home"),$("#userInfo"),$("#withdrawals"),$("#itemList"),$("#cancleOrder"),$("#callOp"),$("#send"));
					var tabTop=document.getElementById("homeTabs").getBoundingClientRect().top;
					var headerBottom=document.getElementById("homeHeader").getBoundingClientRect().bottom;
					showHide($("#homeTabs"),$("#homeTabsFixed"));
					break;
				case "#userInfo":
					document.title="我是买手";
					showHide($("#userInfo"),$("#editUserInfo"),$("#user_mobile"),$("#user_email"),$("#user_account"));
					break;
				case "#user_account":
					document.title="提现方式管理";
					showHide($("#user_account"),$("#addAlipay"),$("#addBankCard"),$("#sureToSetDefault"));
					$("#addAlipay").find("input").val("");
					$("#addBankCard").find("input").val("");
					break;
				case "#withdrawals":
					document.title="我的小金库";
					showHide($("#withdrawals"),$("#revenue"),$("#waitRevenue"),$("#user_account"));
					$("#waitRevenue").find(".tabContainer").children().first().addClass("active");
					$("#waitRevenue").find(".tabContainer").children().last().removeClass("active");
					break;
				case "#revenue":
					document.title="我的累计收入";
					showHide($("#revenue"),$("#sureToWithdrawals"))
					break;
				case "#waitRevenue":
					document.title="未入账总额";
					showHide($("#waitRevenue"),$("#itemList"),$("#cancleOrder"),$("#callOp"),$("#send"));
					break;
				case "#itemList":
					document.title="订单详情";
					showHide($("#itemList"),$("#itemDetail"),$("#cancleOrder"),$("#callOp"),$("#send"),$("#exTime"),$("#exSendTime"))
					break;
			}
		}

		window.onscroll=function(event){                     //滚屏tab位置处理
			event.preventDefault();
			var tabTop=document.getElementById("homeTabs").getBoundingClientRect().top;
			var headerBottom=document.getElementById("homeHeader").getBoundingClientRect().bottom;
			if(tabTop<0){
				$("#homeTabsFixed").show();
				$("#homeTabsFixed").animate({opacity:"1"});
				$("#homeTabs").css({"opacity":"0"})
			}else if(headerBottom>0){
				showHide($("#homeTabs"),$("#homeTabsFixed"));
			};
		};

		$("body").on("focus","input,textarea",function(){       //输入区域的默认文字处理
			sellerOrder.placeholder=$(this).attr("placeholder");
			$(this).attr({"placeholder":""})
		})
		$("body").on("blur","input,textarea",function(){
			$(this).attr({"placeholder":sellerOrder.placeholder})
		})

		$("#homeHeader").on("click",".userInfo",function(event){         //进入个人资料页
			event.preventDefault();
			event.stopImmediatePropagation();
			document.title="我是买手";
			showHide($("#userInfo"),$("#home"));
			history.pushState({},"","#userInfo");
		})

		$("#userInfo").on("click","li",function(event){        //修改个人信息
			event.preventDefault();
			event.stopImmediatePropagation();
			if($(this).hasClass("user_mobileContainer")){      //修改手机号
				showHide($("#user_mobile"));
				showHide($("#editUserInfo"),$("#userInfo"));
				history.pushState({},"","#editUserInfo");
				$("#editUserInfo").find(".user_mobile").val($(this).find(".user_mobile").text());
				$("#captchas").val("");
			}else if($(this).hasClass("user_emailContainer")){    //修改邮箱
				showHide($("#user_email"));
				showHide($("#editUserInfo"),$("#userInfo"));
				history.pushState({},"","#editUserInfo");
				$("#editUserInfo").find(".user_email").val($(this).find(".user_email").text());
			}else if($(this).hasClass("user_accountContainer")){            //提现方式方式管理
				setTimeout(function(){showHide($("#user_account"),$("#userInfo"))},50);
				history.pushState({},"","#user_account");
				document.title="提现方式管理"
			}
		})

		$("#editUserInfo").on("click","#getCaptchas",function(event){            //发送验证码操作
			event.preventDefault();
			event.stopImmediatePropagation();
			var data={
				code: clearZero($("#user_mobile").find(".user_code").val()),
				mobile: clearZero($("#user_mobile").find(".user_mobile").val())
			};
			if(!sellerOrder.codePatt.test(data.code)){
				catchAttention($(".editUserInfoContainer").find(".user_code").eq(0))
				alert("请填写您所在国家/地区代码(如中国:86)");
				return false;
			};
			if(!sellerOrder.mobilePatt.test(data.mobile)){
				alert("请输入正确的手机号");
				return false;
			};
			var that=this;
			var time=50;
			showHide($("#loading"));
			$.ajax({
				url:"authMobile",                             //验证用户手机是否注册过的url
				type:"post",
				dataType:"json",
				data:data,
		        beforeSend: function (xhr) {
		            var token = $("meta[name=csrf-token]").attr('content');
		            if (token) {
		                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
		            }
		        },
				success:function(response){
	            	if(response==0){
	            		$("#loading").hide();
	            		alert("此手机已经注册过，请更换手机号码")
	            	}else{
	            		$.ajax({                                                  //发送验证码请求
	            			url: "/seller/getVerifyCode/"+data.code+"/"+data.mobile,
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
					        		sellerOrder.code=data.code;
									sellerOrder.mobile=data.mobile;
									$("#captchas").val("");
									$("#captchas").focus();
									$(that).hide();
									$("#countDown").attr({"style":"display:inline-block"});
									$("#countDown").text("重发"+"("+50+"s)");
									$("#loading").hide();
									int=setInterval(
										function(){
											time=time-1;
											$("#countDown").text("重发"+"("+time+"s)");
											if(time==0){
												window.clearInterval(int);
												$("#countDown").hide();
												$("#getCaptchas").attr({"style":"display:inline-block"});
											}
									},1000);         //发送成功
					        	}else{
									$("#loading").hide();
									alert("发送失败,请重新尝试")//发送失败
					        	}
					        },
				        	error:function(request,errorType,errorMessage){
				        		$("#loading").hide();
				            	alert("error:"+errorType+";  message:"+errorMessage);
				        	}
	            		});
	            	}
	        	},
	        	error:function(request,errorType,errorMessage){
	            	alert("error:"+errorType+";  message:"+errorMessage);
	        	}
			})
		})

		$("#editUserInfo").on("click",".saveEdit",function(event){   //编辑手机号或邮箱保存修改
			event.preventDefault();
			event.stopImmediatePropagation();
			var index=$(this).parents(".editUserInfoContainer");
			var indexData=index.find("."+index.attr("id")).val();
			var data={};
			var url="";
			if(!(indexData==$("span."+index.attr("id")).text())){
				if(index.attr("id")=="user_mobile"){
					data={
						code:0,
						mobile:0
					};
					data.code=sellerOrder.code;
					data.mobile=sellerOrder.mobile;
					if(!sellerOrder.codePatt.test($(".editUserInfoContainer").find(".user_code").eq(0).val())){
						catchAttention($(".editUserInfoContainer").find(".user_code").eq(0));
						alert("请填写您所在国家/地区代码(如中国:86)");
						return false;
					};
					if(!sellerOrder.mobilePatt.test(data.mobile)){
						catchAttention($(".editUserInfoContainer").find(".user_mobile").eq(0))
						alert("请输入正确的手机号码");
						return false;
					}else{
						if($("#captchas").val()==""){
							alert("请输入验证码");
							catchAttention($("#captchas"));
							return false;
						};
						showHide($("#loading"));
						$.ajax({
							url: "/seller/verifyCode/"+data.code+"/"+data.mobile+"/"+$("#captchas").val(),    //验证请求
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
									$("#countDown").hide();
									$("#getCaptchas").show();
									url="updateMobile";
									$.ajax({
										url:url,
										data:data,
										type:"post",
										dataType:"json",
										beforeSend:function(xhr){
						                    var token = $("meta[name=csrf-token]").attr('content');
						                    if (token) {
						                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
						                    };
										},
										success:function(response){
											if(response==1){
												$("#userInfo").find("."+index.attr("id")).text(indexData);
												$("#loading").hide();
												history.back();
											}else{
												alert("手机号码/邮箱已存在，请重新输入");
												$("#loading").hide()
											}
										},
						                error: function (request, errorType, errorMessage) {
						                    alert("error:" + errorType + ";  message:" + errorMessage);
						                    $("#loading").hide()
						                }
									})
								}else{
									$("#loading").hide();
									alert("验证码错误，请重新输入");                        //验证失败								
								}
							}
						});							
					};
				}else{
					data={email:indexData};
					if(!sellerOrder.emailPatt.test(indexData)){
						catchAttention($(".editUserInfoContainer").find(".user_email").eq(0))
						alert("请输入正确的邮箱");
						return false;
					};
					url="updateEmail";
					showHide($("#loading"));
					$.ajax({
						url:url,
						data:data,
						type:"post",
						dataType:"json",
						beforeSend:function(xhr){
		                    var token = $("meta[name=csrf-token]").attr('content');
		                    if (token) {
		                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
		                    };					
						},
						success:function(response){
							if(response==1){
								$("#userInfo").find("."+index.attr("id")).text(indexData);
								$("#loading").hide()
								history.back();
							}else{
								alert("手机号码/邮箱已存在，请重新输入");
								$("#loading").hide()
							}
						},
		                error: function (request, errorType, errorMessage) {
		                    alert("error:" + errorType + ";  message:" + errorMessage);
		                    $("#loading").hide()
		                }				
					})					
				};
			}else{
				history.back();
			}
		})

		$("#withdrawals").on("click",".account",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#user_account"),$("#withdrawals"));
			history.pushState({},"","#user_account");
			document.title="提现方式管理"
		})

		$("#addAlipay,#addBankCard").on("click",".save",function(event){     //提现方式保存
			event.preventDefault();
			event.stopImmediatePropagation();
			var data;
			var index=$(this).parent("div").parent("div");
			data={
				name: index.find("input").eq(2).val() ,
				identification: index.find("input").eq(0).val(),
				password: index.find("input").eq(3).val()			
			};
			if(index.attr("id")=="addAlipay"){
				data.channel = 2;
				if(!sellerOrder.alipayPatt1.test(index.find("input").eq(0).val())){
					if(!sellerOrder.alipayPatt2.test(index.find("input").eq(0).val())){
						alert("支付宝账号格式不正确。");
						catchAttention(index.find("input").eq(0));
						return false;
					}
				}
			}else{
				data.channel = 1;
				if(!sellerOrder.bankCardPatt.test(index.find("input").eq(0).val())){
					alert("银行卡号位数不正确。")
					catchAttention(index.find("input").eq(0));
					return false;
				}
			};
			if(index.find("input").eq(0).val()!=index.find("input").eq(1).val()){
				catchAttention(index.find("input").eq(1));
				return false;
			};
			for(var i=0; i<index.find("input").length; i++){
				if(i==2){
					if(!isChineseChar(index.find("input").eq(i).val())){
						alert("请输入中文实名");
						catchAttention(index.find("input").eq(i));
						return false;
					}
				}else if(index.find("input").eq(i).val()==""){
					catchAttention(index.find("input").eq(i));
					return false;
				};
			};
			if(sellerOrder.paymentId==0){
				url="/seller/createPayment"
			}else{
				data.payment_id=sellerOrder.paymentId;
				url="/seller/updatePayment"
			}
			showHide($("#loading"));
            $.ajax({
                url:url,
                type:"post",
                data:data, 
                dataType:"json",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	if(response==0){
                		alert("安全密码错误，请重新输入或联系客服。")
                	}else if(response==-1){
                		if(data.channel==2){
                			alert("该支付宝已被使用，请更换。");
                		}else{
                			alert("该银行卡号已被使用，请更换。");
                		}
                	}else if(response==-2){
                		alert("暂不支持该银行卡号，请检查是否输入错误。")
                	}else{
	                	if(data.channel==2){
	                		var newPayment=$("#user_account").find(".alipayOuter");
	                		newPayment.find("td").last().html("<span class='default'>默认</span>");
	                		$("#user_account").find(".bankCardOuter").find("td").last().html("<span class='setDefault'>设为默认</span>");
	                		$("#user_account").find(".addAlipay").parent("div").remove();
	                	}else{
	                		var newPayment=$("#user_account").find(".bankCardOuter");
	                		newPayment.find(".bank_info").text(response.back_info.card_name);
	                		newPayment.find("td").last().html("<span class='default'>默认</span>");
	                		$("#user_account").find(".alipayOuter").find("td").last().html("<span class='setDefault'>设为默认</span>");
	                		$("#user_account").find(".addBankCard").parent("div").remove();
	                	};
	                	$("#user_account").find(".instruction").hide();
	            		newPayment.show();
	            		newPayment.find(".buyer_name").text(data.name);
	            		newPayment.data("payment-id",response.payment_id);
	            		newPayment.find(".identification").text(response.identification);
	                	history.back();
	                };
                    $("#loading").hide();
                },
                error: function (request, errorType, errorMessage) {
                    alert("出现错误，请关闭页面重新进入。");
                    $("#loading").hide();
                }
	        })
		})

		$("#user_account").on("click",".addNewPayMethod",function(event){     //添加新的提现方式
			event.preventDefault();
			event.stopImmediatePropagation();
			if($(this).hasClass("addAlipay")){
				setTimeout(function(){showHide($("#addAlipay"),$("#user_account"))},50);
				history.pushState({},"","#addAlipay");
				document.title="添加支付宝";
			}else{
				setTimeout(function(){showHide($("#addBankCard"),$("#user_account"))},50);
				history.pushState({},"","#addBankCard");
				document.title="添加银行卡";
			}
		})

		$("#user_account").on("click",".changeAlipay",function(event){        //修改支付宝
			event.preventDefault();
			event.stopImmediatePropagation();
			sellerOrder.paymentId=$(this).parents(".alipayOuter").data("payment-id");
			setTimeout(function(){showHide($("#addAlipay"),$("#user_account"))},50);
			history.pushState({},"","#addAlipay");
			document.title="更改支付宝";
		})

		$("#user_account").on("click",".changeBankCard",function(event){    //修改银行卡
			event.preventDefault();
			event.stopImmediatePropagation();
			sellerOrder.paymentId=$(this).parents(".bankCardOuter").data("payment-id");
			setTimeout(function(){showHide($("#addBankCard"),$("#user_account"))},50);
			history.pushState({},"","#addBankCard");
			document.title="更改银行卡";
		})

		$("#user_account").on("click",".icon-bin",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			$(that).hide();
			$(that).css({opacity:"0"});
			$(that).next("span").show();
			$(that).next("span").animate({"opacity":"1"});
			setTimeout(function(){
				$(that).next("span").hide();
				$(that).next("span").css({opacity:"0"});
				$(that).show();
				$(that).animate({"opacity":"1"});
			},1500)
		})

		$("#user_account").on("click",".delete",function(event){                //删除提现方式页
			event.preventDefault();
			event.stopImmediatePropagation();
			var index=$(this).parents("li");
			if(index.hasClass("alipayOuter")){
				var newButton=$("<div class='editPayMethod'><div class='addNewPayMethod addAlipay'><p>添加支付宝</p></div></div>");		
				showHide($("#loading"));
				$.ajax({
					url:"/seller/deletePayment/"+index.data("payment-id"),
					type:"get",
					dataType:"json",
					beforeSend:function(xhr){
	                    var token = $("meta[name=csrf-token]").attr('content');
	                    if (token) {
	                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                    };					
					},
					success:function(response){
						if(response==1){
							index.hide();
							$("#user_account").find(".bankCardOuter").find("td").last().html("<span class='default'>默认</span>");
							sellerOrder.paymentId=0;
							$("#user_account").append(newButton);
						}else{
							alert("删除失败，请重新操作")
						};
						$("#loading").hide();
					},
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").hide();
	                }				
				})				
			}else{
				var newButton=$("<div class='editPayMethod'><div class='addNewPayMethod addBankCard'><p>添加银行卡</p></div></div>");
				showHide($("#loading"));
				$.ajax({
					url:"/seller/deletePayment/"+index.data("payment-id"),
					type:"get",
					dataType:"json",
					beforeSend:function(xhr){
	                    var token = $("meta[name=csrf-token]").attr('content');
	                    if (token) {
	                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                    };					
					},
					success:function(response){
						if(response==1){
							index.hide();
							$("#user_account").find(".alipayOuter").find("td").last().html("<span class='default'>默认</span>");
							sellerOrder.paymentId=0;
							$("#user_account").append(newButton);
						}else{
							alert("删除失败，请重新操作")
						};
						$("#loading").hide();
					},
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").hide();
	                }				
				})				
			};
			if($("#user_account").find(".editPayMethod").length==2){
				var newDescription=$("<div class='instruction'><p>添加银行卡或支付宝才能提现哦~</p></div>");
				$("#user_account").prepend(newDescription);
			}
		})

		$("#user_account").on("click",".setDefault",function(event){           //设为默认提现方式
			event.preventDefault();
			event.stopImmediatePropagation();
			sellerOrder.setDefaultIndex=$(this).parents("li");
			$("#sureToSetDefault").find(".payment_name").text(sellerOrder.setDefaultIndex.find(".payment_name").text()+"("+sellerOrder.setDefaultIndex.find(".identification").text()+")");
			$("#sureToSetDefault").find(".sureToSetDefault").data("payment-id",sellerOrder.setDefaultIndex.data("payment-id"));
			showHide($("#sureToSetDefault"));
			history.pushState({},"","#sureToSetDefault");
		})

		$("#sureToSetDefault").on("click",".sureToSetDefault",function(event){        //设为默认操作
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			var data={
				payment_id: $(that).data("payment-id")
			};
			showHide($("#loading"));
            $.ajax({
                url:"/seller/setToDefault",
                type:"post",
                data:data, 
                dataType:"json",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	$("#user_account").find(".default").text("设为默认").addClass("setDefault").removeClass("default");
                	sellerOrder.setDefaultIndex.find(".setDefault").text("默认").addClass("default").removeClass("setDefault");
                	history.back();
                	$("#loading").hide();
                },
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                    $("#loading").hide();
                }
	        })
		})

		$("#editUserInfo").on("click",".clear",function(event){          //清除之前的输入
			event.preventDefault();
			event.stopImmediatePropagation();
			$(this).parents("td").prev("td").find("input").val("");
			$(this).parents("td").prev("td").find("input").focus();
		})

		$("#homeHeader").on("click",".account",function(event){          //拉起提现页
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#withdrawals"),$("#home"));
			history.pushState({},"","#withdrawals");
			document.title="我的小金库";
		})

		$("#withdrawals").on("click",".toRevenue",function(event){        //进入累计收入页
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"));
            $.ajax({
                url:"/seller/toggleToRevenue",
                type:"get", 
                dataType:"html",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	$("#revenue").find(".going").html(response);
					showHide($("#revenue").find(".going"),$("#loading"));
                },
                error: function (request, errorType, errorMessage) {
					alert("加载失败，请重试");
					showHide($("#revenue").find(".going"),$("#loading"));
                }
	        });
            $("#revenue").find(".tabContainer").find(".active").removeClass("active");
			showHide($("#revenue"),$("#withdrawals"));
			showHide($("#revenue").find(".going").eq(0),$("#revenue").find(".completed").eq(0));
			$("#revenue").find(".toGoing").parents("li").addClass("active");
			history.pushState({},"","#revenue");
			document.title="我的累计收入";
		})

		$("#withdrawals").on("click",".toSend",function(event){        //进入未入账总额页
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"),$("#waitRevenue").find(".orderContainer"));
            $.ajax({
                url:"toggleToNeedToDelivery",
                type:"get",
                dataType:"html",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	$("#waitRevenue").find(".orderContainer").html(response);
					showHide($("#waitRevenue").find(".orderContainer"),$("#loading"));
                },
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
					showHide($("#waitRevenue").find(".orderContainer"),$("#loading"));
                }
	        });
			showHide($("#waitRevenue"),$("#withdrawals"));
			history.pushState({},"","#waitRevenue");
			document.title="未入账总额";
		})	

		$("#revenue").on("click",".toGoing,.toCompleted",function(event){      //可提现和提现记录tab切换
			event.preventDefault();
			event.stopImmediatePropagation();
			$("#revenue").find(".tabContainer").find(".active").removeClass("active");
			if($(this).hasClass("toGoing")){
				showHide($("#loading"));
				showHide($("#revenue").find(".going").eq(0),$("#revenue").find(".completed").eq(0));
	            $.ajax({
	                url:"/seller/toggleToRevenue",
	                type:"get",
	                dataType:"html",
	                beforeSend: function (xhr) {
	                    var token = $("meta[name=csrf-token]").attr('content');
	                    if (token) {
	                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                    };
	                },
	                success: function (response) {
	                	$("#revenue").find(".going").html(response);
						showHide($("#revenue").find(".going"),$("#loading"));               	
	                },
	                error: function (request, errorType, errorMessage) {
						alert("加载失败，请重试");
						showHide($("#revenue").find(".going"),$("#loading"));
	                }
		        });
			}else{
				showHide($("#loading"));
				showHide($("#revenue").find(".completed").eq(0),$("#revenue").find(".going").eq(0))
	            $.ajax({
	                url:"/seller/toggleToWithdraw",
	                type:"get", 
	                dataType:"html",
	                beforeSend: function (xhr) {
	                    var token = $("meta[name=csrf-token]").attr('content');
	                    if (token) {
	                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                    };
	                },
	                success: function (response) {
	                	$("#revenue").find(".completed").html(response);
						showHide($("#revenue").find(".completed"),$("#loading"));
	                },
	                error: function (request, errorType, errorMessage) {
						alert("加载失败，请重试");
						showHide($("#revenue").find(".completed"),$("#loading"));
	                }
		        });
			}
			$(this).parents("li").addClass("active");
		})

		$("#waitRevenue").on("click",".toWaitSend,.toAuditing",function(event){      //未发货和审核中tab切换
			event.preventDefault();
			event.stopImmediatePropagation();
			$("#waitRevenue").find(".tabContainer").find(".active").removeClass("active");
			showHide($("#loading"),$("#waitRevenue").find(".orderContainer"));
			if($(this).hasClass("toWaitSend")){
	            $.ajax({
	                url:"toggleToNeedToDelivery",
	                type:"get",
	                dataType:"html",
	                beforeSend: function (xhr) {
	                    var token = $("meta[name=csrf-token]").attr('content');
	                    if (token) {
	                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                    };
	                },
	                success: function (response) {
	                	$("#waitRevenue").find(".orderContainer").html(response);
						showHide($("#waitRevenue").find(".orderContainer"),$("#loading"));
	                },
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
						showHide($("#waitRevenue").find(".orderContainer"),$("#loading"));
	                }
		        });
			}else{
	            $.ajax({
	                url:"toggleToAuditing",
	                type:"get",
	                dataType:"html",
	                beforeSend: function (xhr) {
	                    var token = $("meta[name=csrf-token]").attr('content');
	                    if (token) {
	                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                    };
	                },
	                success: function (response) {
	                	$("#waitRevenue").find(".orderContainer").html(response);
						showHide($("#waitRevenue").find(".orderContainer"),$("#loading"));
	                },
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
						showHide($("#waitRevenue").find(".orderContainer"),$("#loading"));
	                }
		        });
			}
			$(this).parents("li").addClass("active");
		})

		$("#revenue").on("click",".toWithdrawals",function(event){        //弹出确认提现弹窗
			event.preventDefault();
			event.stopImmediatePropagation();
			if($("#user_account").find(".editPayMethod").length==2){
				alert("请至“提现方式管理”添加提现方式！");
				return false;
			};
			var that=this;
			var payment=$("#user_account").find(".default").parents("table");
			var modal=$("#sureToWithdrawals");
			modal.find(".payment_name").text(payment.find(".payment_name").text());
			modal.find(".identification").text(payment.find(".identification").text());
			modal.find(".buyer_name").text(" "+payment.find(".buyer_name").text());
			modal.show();
			modal.animate({opacity:"1"});
			modal.find(".sureToWithdrawals").data("order-id",$(that).parents("li").find(".order-id").text());
			modal.find(".amount").text($(that).parents("li").find(".amount").text());
			sellerOrder.editIndex=$(that).parents("li").index();
			history.pushState({},"","#sureToWithdrawals");
		})

		$("#sureToWithdrawals").on("click",".sureToWithdrawals",function(event){            //确认提现
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			showHide($("#loading"));
            $.ajax({
                url:"/seller/applyWithdraw/"+$(that).data("order-id"),
                type:"get", 
                dataType:"json",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	if(response==1){
                		$("#loading").hide();
                		$("#revenue").find(".going").find("li").eq(sellerOrder.editIndex).remove();
                		history.back();
                	}else{
                		$("#loading").hide();
                		alert("提现失败，稍后请重试")
                	}             	
                },
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                    $("#loading").hide();
                }
	        })
		})

		$("#homeTabs").on("click",".homeTab",function(event){        //选中tab处理
			event.preventDefault();
			event.stopImmediatePropagation();
			$(this).parents("#homeTabs").find(".active").removeClass("active");
			$(this).addClass("active");
			$("#homeTabsFixed").find(".active").removeClass("active");
			$("#homeTabsFixed").find("li").eq($(this).index()).addClass("active");
			showHide($("#loading"),$("#orderContent"));
			var that=this;
			changeTab(that);			
		})
		$("#homeTabsFixed").on("click",".homeTab",function(event){    //选中tab处理
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"),$("#orderContent"));
			window.scrollTo(0,0);
			showHide($("#homeTabs"),$("#homeTabsFixed"));
			$(this).parents("#homeTabsFixed").find(".active").removeClass("active");
			$(this).addClass("active");
			$("#homeTabs").find(".active").removeClass("active");
			$("#homeTabs").find("li").eq($(this).index()).addClass("active");
			var that=this;
			changeTab(that);
		})

		if(location.hash=="#toDeliver"){                                     //hash定锚点
			$("#waitSend").trigger("click");
			$("#loading").animate({background:"rgba(255,255,255,0)"});
			history.replaceState({},"","#home");
		}else{
			if(location.hash!="#toWithdraw"){
				$("#loading").animate({background:"rgba(255,255,255,0)"});
				history.replaceState({},"","#home");
			};
		    $.ajax({
	            url:"toggleToReceived",
	            type:"get",
	            dataType:"html",
	            beforeSend: function (xhr) {
	                var token = $("meta[name=csrf-token]").attr('content');
	                if (token) {
	                    return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	                };
	            },
	            success: function (response) {
	            	$("#orderContent").find(".orderContainer").html(response);
	            	showHide($("#orderContent"),$("#loading"));
	            	checkOrderNum();
	            },
	            error: function (request, errorType, errorMessage) {
	                alert("error:" + errorType + ";  message:" + errorMessage);
	                $("#loading").hide();
	            }
	        })

		    $.ajax({
		        url:"toggleToNeedToDelivery",
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
		        		$(".catchAttention").css({opacity:"0"})
		        	}else{
		        		$(".catchAttention").css({opacity:"1"});
		        		$(".catchAttention").text(($(response).length+1)/2);
		        	}
		        },
		        error: function (request, errorType, errorMessage) {
		            alert("error:" + errorType + ";  message:" + errorMessage);
		        }
		    })
		};
		
		if(location.hash=="#toWithdraw"){                     //带有#toWithdraw hash时跳到提现页
			history.replaceState({},"","#home");
			$(".account").eq(0).trigger("click");
			$("#loading").animate({background:"rgba(255,255,255,0)"});
		};

		$("#homeContent,#waitRevenue").on("click",".orderBody",function(event){           //点击订单查看商品列表
			event.preventDefault();
			event.stopImmediatePropagation();
			var order=$(this).parents("li");
			var lenItem=JSON.parse(order.data("item-title")).length;
			var lenList=$("#itemListContent").find(".itemContainer").length;
			for(var i=lenList; i<lenItem; i++){
				var newItem=$("#itemListContent").find(".itemContainer").eq(0).clone();
				$("#itemListContent").append(newItem);
				lenList++;
			};
			for(var i=lenList; i>lenItem; i--){
				$("#itemListContent").find(".itemContainer").last().remove();
				lenList--;
			};
			for(var i=0; i<lenItem; i++){
				if(JSON.parse(order.data("item-url"))[i].length>0){
					$("#itemList").find(".itemImage").eq(i).attr({"src":JSON.parse(order.data("item-url"))[i][0]});
				}else{
					$("#itemList").find(".itemImage").eq(i).attr({"src":"/image/DefaultPicture.jpg"});
				};
				$("#itemList").find(".itemContainer").eq(i).data("item-title",JSON.parse(order.data("item-title"))[i]);
				if(JSON.parse(order.data("item-title"))[i].length>30){
					$("#itemList").find(".itemTitle").eq(i).text(JSON.parse(order.data("item-title"))[i].substr(0,30)+"...");
				}else{
					$("#itemList").find(".itemTitle").eq(i).text(JSON.parse(order.data("item-title"))[i]);
				};
				$("#itemList").find(".itemNumber").eq(i).text(JSON.parse(order.data("item-number"))[i]);
				if(!(order.data("item-memos")=="")){
					$("#itemList").find(".itemContainer").eq(i).data("item-memos",JSON.parse(order.data("item-memos"))[i])
				}else{
					$("#itemList").find(".itemContainer").eq(i).data("item-memos","");
				}
				$("#itemList").find(".itemContainer").eq(i).data("item-description",JSON.parse(order.data("item-description"))[i]);
				$("#itemList").find(".itemContainer").eq(i).data("item-url",JSON.stringify(JSON.parse(order.data("item-url"))[i]));
				$("#itemList").find(".itemContainer").eq(i).find(".price").text(JSON.parse(order.data("item-price"))[i]);
			};
			$("#itemList").find(".postage").text(order.data("order-postage"));
			$("#itemList").find(".itemTotalNumber").text(order.find(".totalNumber").text());
			var index=$("#itemList");
			$("#orderButtonFixed").html(order.clone());
			$("#itemListfooter").find("p").hide();
			$("#itemListfooter").find(".exSendTime").hide();
			$("#itemListfooter").find("p").eq(0).show();
			if(order.data("item-status")>0){
				$("#itemDetail").find(".descriptionTitle").eq(1).css({opacity:"1"});
				$("#itemDetail").find(".opNote").css({opacity:"1"});
				index.find(".orderPriceOuter").css({opacity:"1"});
				index.find(".orderPrice").text(order.find(".orderPrice").text());
				index.find(".idOuter").text("订单号：");
				index.find(".id").text(order.find(".order_id").text());
				$("#logistics").find(".receiving_info").find(".receiver").text(order.data("item-receiver-name")+"，");
				$("#logistics").find(".receiving_info").find(".receiver_mobile").text(order.data("item-receiver-mobile"));
				$("#logistics").find(".receiving_info").find(".receiving_address").text(order.data("item-receiving-address"));
			};
			switch(order.data("item-status")){
				case "1":
					index.find(".itemStatus").text("等待买家付款");
					index.find(".offerTimeOuter").show();
					index.find(".offerTime").text(order.data("order-offer-time"));
					index.find(".exTimeOuter").show();
					index.find(".exTimeInner").text(order.data("order-ex-time"));
					$("#logistics").hide();
					document.title="订单详情"
					break;
				case "2":
					index.find(".itemStatus").text(order.find(".orderHeader").find("span").last().text());
					index.find(".offerTimeOuter").show();
					index.find(".offerTime").text(order.data("order-offer-time"));
					index.find(".payTimeOuter").show();
					index.find(".payTime").text(order.data("order-pay-time"))
					if(!order.hasClass("auditing")){
						index.find(".exSendTime").show();
					}else{
						index.find(".sendTimeOuter").show();
						index.find(".sendTime").text(order.data("order-send-time"));
					};
					$("#logistics").show();
					$("#logistics").find(".receiving_info").show();
					$("#logistics").find(".logistics_info").hide();
					document.title="订单详情"
					break;
				case "3":
					index.find(".itemStatus").text("已发货");
					index.find(".offerTimeOuter").show();
					index.find(".offerTime").text(order.data("order-offer-time"));
					index.find(".payTimeOuter").show();
					index.find(".payTime").text(order.data("order-pay-time"));
					index.find(".sendTimeOuter").show();
					index.find(".sendTime").text(order.data("order-send-time"));
					index.find(".auditingTimeOuter").show();
					index.find(".auditingTime").text(order.data("order-audit-time"));
					$("#logistics").show();
					$("#logistics").find(".receiving_info").show();
					$("#logistics").find(".logistics_info").show();
					$("#logistics").find(".logistics_company").text(order.data("delivery-company"));
					$("#logistics").find(".logistics_number").text(order.data("delivery-number"));
					document.title="订单详情"
					break;
				case "4":
					index.find(".itemStatus").text("已完成");
					index.find(".offerTimeOuter").show();
					index.find(".offerTime").text(order.data("order-offer-time"));
					index.find(".payTimeOuter").show();
					index.find(".payTime").text(order.data("order-pay-time"));
					index.find(".sendTimeOuter").show();
					index.find(".sendTime").text(order.data("order-send-time"));
					index.find(".auditingTimeOuter").show();
					index.find(".auditingTime").text(order.data("order-audit-time"));
					index.find(".completedTimeOuter").show();
					index.find(".completedTime").text(order.data("order-completed-time"));
					$("#logistics").show();
					$("#logistics").find(".receiving_info").show();
					$("#logistics").find(".logistics_info").show();
					$("#logistics").find(".logistics_company").text(order.data("delivery-company"));
					$("#logistics").find(".logistics_number").text(order.data("delivery-number"));
					document.title="订单详情"
					break;
			}
			showHide($("#itemList"),$("#home"),$("#waitRevenue"));
			history.pushState({},"","#itemList");
			window.scrollTo(0,0);
		})

		$("#itemList").on("click",".itemContainer",function(event){           //点击商品查看商品详情
			event.preventDefault();
			event.stopImmediatePropagation();
			document.title="商品详情";
			var urlArray=JSON.parse($(this).data("item-url"));
			var divLen=$("#itemImageContainer").find("div").length;
			if(urlArray.length==0){
				urlArray.push("/image/DefaultPicture.jpg");
			}
			for(var i=divLen; i<urlArray.length; i++){
				var newDiv=$("#itemImageContainer").find("div").eq(0).clone();
				$("#itemImageContainer").append(newDiv);
				divLen++;
			};
			for(var i=divLen; i>urlArray.length; i--){
				$("#itemImageContainer").find("div").last().remove();
				divLen--;
			};
			for(var i=0; i<urlArray.length; i++){
				$("#itemImageContainer").children("div").eq(i).find("img").attr({"src":urlArray[i]});
			};
			$("#itemTitleContainer").find(".itemTitle").text($(this).data("item-title"));
			$("#itemDescriptionContainer").find(".itemDescription").text($(this).data("item-description"));
			$("#itemDescriptionContainer").find(".opNote").text($(this).data("item-memos"));
			showHide($("#itemDetail"),$("#itemList"));
			$("#itemImageContainer").height($("#itemImageContainer").find("img").parent("div").height()*Math.ceil($("#itemImageContainer").find("img").length/3));
			history.pushState({},"","#itemDetail");
			window.scrollTo(0,0);
		})

		$("#homeContent,#itemList,#waitRevenue").on("click",".cancleOrder",function(event){   //弹出取消订单弹窗
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			// sellerOrder.editIndex=$(that).parents(".order").index();
			$("#orderIndex").removeAttr("id");
			$(that).parents(".order").attr({"id":"orderIndex"});
			$("#cancleOrder").find(".sureToCancle").data("order-id",$(that).parents("li").find(".order_id").text());
			$("#cancleOrder").find(".sureToCancle").data("order-index",$(that).parents("li").index());
			$("#cancleOrder").find(".sureToCancle").data("order-status",$(that).parents("li").data("item-status"));
			$("#cancleOrder").show();
			$("#cancleOrder").animate({opacity:"1"});
			history.pushState({},"","#cancleOrder");
		})
		$("#cancleOrder").on("click",".sureToCancle",function(event){                //买手拒绝订单
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"));
            $.ajax({
                url:"/seller/cancelSeller/"+$("#orderIndex").find(".order_id").text(),
                type:"get",
                dataType:"json",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	if(response==1){
                		var orderIdConfirm=$("#orderIndex").find(".order_id").text();
                		$("#orderIndex").remove();
						$("#loading").hide();
	                	checkOrderNum();
						history.back();
						setTimeout(function(){
							if(location.hash=="#itemList"){
								for(var i=0; i<$(".order_id").length; i++){
									if(orderIdConfirm==$(".order_id").eq(i).text()){
										$(".order_id").eq(i).parents(".order").remove();
									};
								};
								history.back();
							};
	                		if($("#orderContent").find(".orderContainer").find(".order").length>0){
	                			$(".catchAttention").text($("#orderContent").find(".orderContainer").find(".order").length);
	                		}else{
	                			$(".catchAttention").css({opacity:"0"})
	                		};
						},10)
                	}else{
                		$("#loading").hide();
                		alert("删除失败，请重新操作");
                	}
                },
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                    $("#loading").hide();
                }
	        })
		})

		$("#homeContent,#itemList,#waitRevenue").on("click",".send",function(event){          //弹出填写物流单弹窗
			event.preventDefault();
			event.stopImmediatePropagation();
			// sellerOrder.editIndex=$(this).parents(".order").index();
			$("#orderIndex").removeAttr("id");
			$(this).parents(".order").attr({"id":"orderIndex"});
			$("#send").find(".sureToSend").data("order-id",$(this).parents(".order").find(".order_id").text());
			setTimeout(function(){
				$("#send").show();
				$("#send").animate({opacity:"1"});
				history.pushState({},"","#send");
			},50)
		})
		$("#send").on("change",".logistics",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			if($(this).val()=="otherCompany"){
				$("#send").find(".logistics_other").show();
			}else{
				$("#send").find(".logistics_other").hide();
			}
		})
		$("#send").on("click",".sureToSend",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			var data={
				logistics_id:$("#send").find(".logistics").val(),
				logistics_name:$("#send").find(".logistics_other").val(),
				logistics_pinyin:$("#send").find("option").eq($("#send").find(".logistics").val()).data("pinyin"),
				logistics_number:$("#send").find(".logistics_number").val()
			};
			if(data.logistics_id == 0){
				alert("请选择快递公司");
				return false
			}
			if(data.logistics_id == "otherCompany"&&data.logistics_name == ""){
				alert("请填写快递公司名称");
				return false
			};
			if(data.logistics_id != "otherCompany"){
				data.logistics_name="";
			}
			if(data.logistics_number==""){
				alert("请填写快递单号");
				return false
			};
			if(data.logistics_pinyin==undefined){
				data.logistics_pinyin=""
			};
			var that=this;
			showHide($("#loading"));
            $.ajax({
                url:"/seller/createDeliveryInfo/"+$(that).data("order-id"),
                type:"post",
                data:data,
                dataType:"json",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    };
                },
                success: function (response) {
                	if(response==1){
                		var orderIdConfirm=$("#orderIndex").find(".order_id").text();
                		if($("#orderIndex").parent("ul").parent("div").attr("id")=="orderContent"){
                			$("#orderIndex").remove();
                		}else{
                			$("#orderIndex").hide();
                			$("#orderIndex").find(".send").remove();
                			$("#orderIndex").find(".cancleOrder").remove();
                			$("#orderIndex").find(".orderHeader").find("span").last().text("审核中");
                			$("#orderIndex").data("item-status","3");
                			$("#orderIndex").attr({"class":"order auditing"});
                		};
						$("#send").find("input").val("");
						$("#send").find("select").find("option").eq(0).attr({"selected":"selected"});
                		history.back();
						setTimeout(function(){
							if(location.hash=="#itemList"){
								history.back();
								for(var i=0; i<$(".order_id").length; i++){
									if(orderIdConfirm==$(".order_id").eq(i).text()){
										$(".order_id").eq(i).parents(".order").remove();
									};
								};
							};
	                		if($("#orderContent").find(".orderContainer").find(".order").length>0){
	                			$(".catchAttention").text($("#orderContent").find(".orderContainer").find(".order").length);
	                		}else{
	                			$(".catchAttention").css({opacity:"0"})
	                		};
						},10)
                	}else{
                		alert("发货失败，稍后请重试")
                	}
                	$("#loading").hide();
                },
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                    $("#loading").hide();
                }
	        })
		})

		$("#homeContent,#itemList,#waitRevenue").on("click",".callOp,.exTime,.exSendTime",function(event){    //弹窗处理
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			if($(that).hasClass("callOp")){
				$("#callOp").find(".opTel").text($(that).parents("li").data("item-operator-mobile"));
				$("#callOp").find(".call").find("a").attr({"href":"tel:0086"+$(that).parents("li").data("item-operator-mobile")});
			};
			$("#"+$(that).attr("class")).show();
			$("#"+$(that).attr("class")).animate({"opacity":"1"});
			history.pushState({},"","#"+$(this).attr("class"));
		})

		$("body").on("click",".keepItOn",function(event){     //关闭弹窗
			event.preventDefault();
			event.stopImmediatePropagation();
			history.back();
			$("#send").find("input").val("");
			$("#send").find("select").find("option").eq(0).attr({"selected":"selected"});
		})

	})