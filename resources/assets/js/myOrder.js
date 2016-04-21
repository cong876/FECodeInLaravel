	window.addEventListener( "load", function() {
	    FastClick.attach( document.body );
	}, false );

	$(document).ready(function(){

		$("#province").find("option").eq(0).attr({"selected":"selected"});

		$("#homeHeader").height(document.body.scrollWidth*0.376);
		$("#homeTabs").height(document.body.scrollWidth*0.173);
		$("#wx_image").height(document.body.scrollWidth*0.2);

		var myOrder={                                           //全局变量打包
			mobilePatt:/^[0-9]{11}$/,
			emailPatt:/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/,
			addressIndex:-1,
			mobile:0,
			placeholder:"",
			addressSelect:0
		}
		var hasAnchor = 0;
		var localhash = location.hash;
		var int;

		var showHide=function(){                                //页面切换
			var test=arguments;
			for(var i=0; i<test.length; i++){
				if(i>0){
					test[i].hide();
					test[i].css({"opacity":"0"});
				}
			};
			if((test[0].attr("id")=="orderContent")||(test[0].attr("id")=="waitOfferContent")||(test[0].attr("class")=="icon-bin")||(test[0].attr("class")=="deleteAddress")||(test[0].attr("class")=="noOrderContent")||(test[0].attr("id")=="home")){
				test[0].show();
				test[0].animate({"opacity":"1"},300);
			}else{
				test[0].show();
				test[0].css({"opacity":"1"});
				test[0].height(test[0].height());
			};
		}

		var checkOrderNum=function(indexTab){                           //检测当前tab下订单数量
			if(indexTab.find(".order").length>0){
				$(".noOrderContent").css({"opacity":"0"});
				$(".noOrderContent").hide();
			}else{
				showHide($(".noOrderContent"));
			}
		}

		var saveAddressToFrontPage=function(address,target){           //保存地址信息到list的data里
			target.data("city-code",address.cityCode);
			target.data("city",address.city);
			target.data("county-code",address.countyCode);
			target.data("county",address.county);
			target.data("receiver_area",address.selected)
		}

		var setDefaultAddress=function(addressIndex){                     //设置默认地址
			for(var i=0; i<$(".editAddressesContent").find(".default").length; i++){
				$(".editAddressesContent").find(".default").eq(i).text("");
			};
			$(".editAddressesContent").find(".checked").removeClass("checked");
			addressIndex.addClass("checked");
			addressIndex.find(".default").text("[默认]");
		}

		var catchAttention=function(needBorder){                         //未填信息指出
			needBorder.css({borderColor:"rgba(255,0,0,1)"});
			setTimeout(function(){
				needBorder.css({borderColor:"rgba(255,255,255,0)"})
			},2000);
		}

		var changeTab=function(that){			
			if(!(($(that).hasClass("waitOffer"))||($(that).attr("id")=="waitOffer"))){
				var url;
				switch($(that).index()){
					case 1:   url="toggleToNeedToPay"
								break;
					case 2:  url="toggleToNeedToDelivery"
								break;
					case 3:      url="toggleToHasDelivered"
								break;
					case 4: url="toggleToHasFinished"
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
						showHide($("#orderContent"),$("#waitOfferContent"),$("#loading"));
						$("#loading").animate({"backgroundColor":"rgba(255,255,255,0)"});
						checkOrderNum($("#orderContent"));
	                	if(url=="toggleToNeedToPay"){
	                		$(".catchAttention").text($("#orderContent").find(".orderContainer").find(".order").length);
			            	if(response==""){
			            		$(".catchAttention").css({opacity:"0"})
			            	}else{
			            		$(".catchAttention").css({opacity:"1"})
			            	};
			            	if(hasAnchor == 1){
								for(var i=0; i<$(".order_id").length; i++){
									if($(".order_id").eq(i).text()==localhash.substr(10,localhash.length)){
										console.log($(".order_id").eq(i));
										$(".order_id").eq(i).parents(".order").find(".payOrder").trigger("click");
									};
								};
								hasAnchor = 0;
			            	};
	                	};
	                },
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").css({opacity:"0"});
	                    $("#loading").hide();
	                }
	            })
			}else{
			    $.ajax({
			        url:"toggleToNeedToSendPrice",
			        type:"get",
			        dataType:"html",
			        beforeSend: function (xhr) {
			            var token = $("meta[name=csrf-token]").attr('content');
			            if (token) {
			                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
			            };
			        },
			        success: function (response) {
	                	$("#waitOfferContent").find(".orderContainer").html(response);
						showHide($("#waitOfferContent"),$("#orderContent"),$("#loading"));
						$("#loading").animate({"backgroundColor":"rgba(255,255,255,0)"});
						checkOrderNum($("#waitOfferContent"));
			        },
			        error: function (request, errorType, errorMessage) {
			            alert("error:" + errorType + ";  message:" + errorMessage);
			            $("#loading").css({opacity:"0"});
			            $("#loading").hide();
			        }
			    });
			}
		}

		window.onpopstate=function(event){
			event.preventDefault();
			switch(location.hash){
				case "#home":
					document.title="个人中心";
					showHide($("#home"),$("#userInfo"),$("#itemList"),$("#cancleOrder"),$("#callOp"),$("#checkRefund"),$("#toReceive"),$("#sureToPay"));
					showHide($("#homeTabs"),$("#homeTabsFixed"));
					break;
				case "#userInfo":
					document.title="我的资料";
					showHide($("#userInfo"),$("#editUserInfo"),$("#user_mobile"),$("#user_email"),$("#user_addresses"));
					break;
				case "#editUserInfo":
					showHide($("#editUserInfo"),$("#editUserAddress"));
					showHide($("#user_addresses"));
					myOrder.addressIndex=-1;
					$("#province").find("[selected]").removeAttr("selected");
					$("#province").find("option").eq(0).attr({"selected":"selected"});
					$("#city").html("");
					$("#county").html("");
					for(var i=0; i<$("#editUserAddress").find("input").length; i++){
						$("#editUserAddress").find("input").eq(i).val("")
					};
					$("#setDefaultAddress").val(1);
					for(var i=0; i<$("#editUserAddress").find("select").length; i++){
						$("#editUserAddress").find("select").eq(i).find("[selected]").removeAttr("selected");
					};
					break;
				case "#itemList":
					document.title="订单详情";
					showHide($("#itemList"),$("#itemDetail"),$("#exTime"),$("#cancleOrder"),$("#callOp"),$("#checkRefund"),$("#toReceive"),$("#sureToPay"));
					break;
				case "#sureToPay":
					document.title="确认下单";
					if(myOrder.addressSelect==0){
						var address=$("#receiving_addresses").find(".user_Address").last();
						if($("#receiving_addresses").find(".user_Address").length>0){
							$("#sureToPayHeader").find(".receiver_address").data("receiver-address-id",address.data("receiver-address-id"));
							$("#sureToPayHeader").find(".receiver_name").text(address.data("receiver")+"，");
							$("#sureToPayHeader").find(".receiver_name").parent("p").find("span").eq(0).text("收货人：");
							$("#sureToPayHeader").find(".receiver_mobile").text(address.data("receiver-mobile"));
							$("#sureToPayHeader").find(".receiver_address").text(address.find(".receiver_address").text());
							$("#sureToPayHeader").find(".receiver_address").parent("p").find("span").eq(0).text("收货地址：");
						};
					};
					showHide($("#sureToPay"),$("#receiving_addresses"));
					break;
				case "#receiving_addresses":
					document.title="更改收货地址";
					$("#receiving_addresses").find("div").eq(0).html($("#user_addresses").find(".editAddressesContent").clone());
					$("#receiving_addresses").find(".deleteOut").remove();
					$("#receiving_addresses").find("div").eq(0).prepend('<p class="addressDirect">请选择收货地址</p>');
					showHide($("#receiving_addresses"),$("#editUserAddress"));
					myOrder.addressIndex=-1;
					$("#province").find("[selected]").removeAttr("selected");
					$("#province").find("option").eq(0).attr({"selected":"selected"});
					$("#city").html("");
					$("#county").html("");
					for(var i=0; i<$("#editUserAddress").find("input").length; i++){
						$("#editUserAddress").find("input").eq(i).val("")
					};
					$("#setDefaultAddress").val(1);
					for(var i=0; i<$("#editUserAddress").find("select").length; i++){
						$("#editUserAddress").find("select").eq(i).find("[selected]").removeAttr("selected");
					};
					break;
			}
		}

		window.onscroll=function(event){                     //滚屏tab位置处理
			event.preventDefault();
			var tabTop=document.getElementById("homeTabs").getBoundingClientRect().top;
			var headerBottom=document.getElementById("homeHeader").getBoundingClientRect().bottom;
			if(tabTop<0){
				$("#homeTabsFixed").show();
				$("#homeTabsFixed").animate({opacity:"1"},200);
				$("#homeTabs").css({"opacity":"0"})
			}else if(headerBottom>0){
				showHide($("#homeTabs"),$("#homeTabsFixed"));
			};
		};

		$("body").on("focus","input,textarea",function(){       //输入区域的默认文字处理
			myOrder.placeholder=$(this).attr("placeholder");
			$(this).attr({"placeholder":""})
		})
		$("body").on("blur","input,textarea",function(){
			$(this).attr({"placeholder":myOrder.placeholder})
		})

		$("#wx_image").on("click",function(event){         //进入个人资料页
			event.preventDefault();
			event.stopImmediatePropagation();
			document.title="我的资料";
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
			}else if($(this).hasClass("user_addressesContainer")){    //修改收货地址
				showHide($("#user_addresses"));
				showHide($("#editUserInfo"),$("#userInfo"));
				document.title="收货地址管理";
				history.pushState({},"","#editUserInfo");
			}
		})

		$("#editUserInfo").on("click",".clear",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			$(this).parents("td").prev("td").find("input").val("");
			$(this).parents("td").prev("td").find("input").focus();
		})

		$("#editUserInfo").on("click","#getCaptchas",function(event){            //发送验证码操作
			event.preventDefault();
			event.stopImmediatePropagation();
			var data={
				mobile:$("#user_mobile").find(".user_mobile").val()
			}
			if(!myOrder.mobilePatt.test(data.mobile)){
				alert("请输入正确的手机号");
				return false
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
									myOrder.mobile=data.mobile;
									$("#captchas").val("");
									$("#captchas").focus();
									$(that).hide();
									$("#countDown").attr({"style":"display:inline-block"});
									$("#countDown").text("重发"+"("+50+"s)");
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

		$("#editUserInfo").on("click",".saveEdit",function(event){   //编辑手机号或邮箱保存修改
			event.preventDefault();
			event.stopImmediatePropagation();
			var index=$(this).parents(".editUserInfoContainer");
			var indexData=index.find("."+index.attr("id")).val();
			var data={};
			var url="";
			if(!(indexData==$("span."+index.attr("id")).text())){
				if(index.attr("id")=="user_mobile"){
					data={mobile:0};
					data.mobile=myOrder.mobile;
					if(!myOrder.mobilePatt.test($("#user_mobile").find(".user_mobile").val())){
						catchAttention($(".editUserInfoContainer").find(".user_mobile").eq(0));
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
							url: "/buyPal/verifyCode/"+data.mobile+"/"+$("#captchas").val(),
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
												$("#loading").hide();
												alert("手机号码/邮箱已存在，请重新输入")
											}
										},
						                error: function (request, errorType, errorMessage) {
						                	$("#loading").hide()
						                    alert("error:" + errorType + ";  message:" + errorMessage)
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
						});
					};
				}else{
					data={email:indexData};
					if(!myOrder.emailPatt.test(indexData)){
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
								history.back();
								$("#loading").hide()
							}else{
								$("#loading").hide();
								alert("手机号码/邮箱已存在，请重新输入")
							}
						},
		                error: function (request, errorType, errorMessage) {
		                	$("#loading").hide();
		                    alert("error:" + errorType + ";  message:" + errorMessage);
		                }
					})
				};
			}else{
				history.back();
			}
		})

		$("#user_addresses").on("click",".icon-bin",function(event){             //删除地址所进行的操作
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($(this).next("span"),$(this));
			var that=this;
			setTimeout(function(){showHide($(that),$(that).next("span"))},1500)
		})
		$("#user_addresses").on("click",".deleteAddress",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			showHide($("#loading"));
			$.ajax({
				url:"deleteReceivingAddress/"+$(that).parents("li").data("receiver-address-id"),
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
						$(that).parents("li").css({height:"0",opacity:"0"});
						setTimeout(function(){
							$(that).parents("li").remove();
						},200);
					}else{
						alert("删除失败，请重新操作")
					};
					$("#loading").hide();
					$("#loading").css({opacity:"0"});
				},
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                    $("#loading").hide();
                    $("#loading").css({opacity:"0"});
                }
			})
		})

		$("#user_addresses").on("click",".deleteOut",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
		})

		$("#editUserInfo").on("click",".user_Address",function(event){  //修改地址
			event.preventDefault();
			event.stopImmediatePropagation();
			if($(this).hasClass("checked")){
				$("#setDefaultAddress").attr({"class":"icon-radio-checked"});
			}else{
				$("#setDefaultAddress").attr({"class":"icon-radio-unchecked"});
			}
			myOrder.addressIndex=$(this).index()-1;
			var detail=$("#editUserAddress");
			var area=$(this).data("receiver-area").split(",");
			var cityCode=$(this).data("city-code").split(",");
			var city=$(this).data("city").split(",");
			var countyCode=$(this).data("county-code").split(",");
			var county=$(this).data("county").split(",");
			for(var i=0; i<cityCode.length; i++){
				var newCity=$("<option></option>").attr({"value":cityCode[i]}).text(city[i]);
				$("#city").append(newCity)
			};
			for(var i=0; i<countyCode.length; i++){
				var newCounty=$("<option></option>").attr({"value":countyCode[i]}).text(county[i]);
				$("#county").append(newCounty);
			};
			$("#editUserAddress").find("select").find("[selected]").removeAttr("selected");
			detail.find("#receiver").val($(this).data("receiver"));
			detail.find("#receiver_mobile").val($(this).data("receiver-mobile"));
			detail.find("#receiver_zipCode").val(area[3]);
			detail.find("#receiver_address").val($(this).data("receiver-address"));
			detail.find("#province").find("[value='"+area[0]+"']").attr({"selected":"selected"});
			detail.find("#city").find("[value='"+area[1]+"']").attr({"selected":"selected"});
			detail.find("#county").find("[value='"+area[2]+"']").attr({"selected":"selected"});
			detail.find("#province").find("[value='"+area[0]+"']").attr({"selected":"selected"});
			setTimeout(function(){
				showHide(detail,$("#editUserInfo"),$("#user_addresses"));
			},100);
			history.pushState({},"","#editUserAddress");
		})

		$("#editUserInfo").on("click",".addNewAddress",function(event){  //新建地址
			event.preventDefault();
			event.stopImmediatePropagation();
			if($("#user_addresses").find("li").length==7){
				alert("最多添加6个收货地址");
				return false;
			}
			$("#setDefaultAddress").attr({"class":"icon-radio-checked"});
			showHide($("#editUserAddress"),$("#editUserInfo"));
			history.pushState({},"","#editUserAddress");
		})

		$("#province").on("change",function(event){                //选择省加载市
			event.preventDefault();
			event.stopImmediatePropagation();
			var data=$(this).val();
			if(data==0){
				return false;
			}else if(data==710000){
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
                url:"getSubRegion/"+data,
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

		$("#city").on("change",function(event){                //选择市加载县
			event.preventDefault();
			event.stopImmediatePropagation();
			var data=$(this).val();
			if(data==0){
				return false;
			}
            $("#receiver_zipCode").val("");
            $.ajax({
                url:"getSubRegion/"+data,
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

		$("#county").on("change",function(event){         //选择县添加邮编
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
                url:"getZipCode/"+data,
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

		$("#editUserAddress").on("click",".setAddressContainer",function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			if($("#setDefaultAddress").hasClass("icon-radio-checked")){
				$("#setDefaultAddress").attr({"class":"icon-radio-unchecked"})
			}else{
				$("#setDefaultAddress").attr({"class":"icon-radio-checked"})
			}
		})

		$("#editUserAddress").on("click","#saveAddress",function(event){    //修改后的地址保存
			event.preventDefault();
			event.stopImmediatePropagation();
			if($("#receiver").val()==""){
				catchAttention($("#receiver"));
				return false;
			};
			if(!myOrder.mobilePatt.test($("#receiver_mobile").val())){
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
			}
			var address={                                                  //传给后台的数据
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
			}
			var areaSelected={                                               //保存在上一页的数据
				cityCode:[],
				city:[],
				countyCode:[],
				county:[],
				selected:[]
			};
			for(var i=0; i<$("#city").find("option").length; i++){
				areaSelected.cityCode.push($("#city").find("option").eq(i).val());
				areaSelected.city.push($("#city").find("option").eq(i).text());
			};
			for(var i=0; i<$("#county").find("option").length; i++){
				areaSelected.countyCode.push($("#county").find("option").eq(i).val());
				areaSelected.county.push($("#county").find("option").eq(i).text());
			};
			areaSelected.selected.push($("#province").val(),$("#city").val(),$("#county").val(),$("#receiver_zipCode").val());
			showHide($("#loading"));
			if(myOrder.addressIndex==-1){                            //创建地址操作
				$.ajax({
	                url:"createReceivingAddress",
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
	                		var newAddress=$("#user_addresses").find(".user_AddressEx").clone();
	                		if(address.setDefault==1){
	                			$("#user_addresses").find(".checked").removeClass("checked");
	                			newAddress.attr({"class":"user_Address"});
	                		}else{
	                			newAddress.attr({"class":"user_Address"});
	                		};
	                		newAddress.data("receiver-address-id",response);
							newAddress.data("receiver",address.receiver);
							newAddress.find(".receiver").text(address.receiver);
							newAddress.data("receiver-mobile",address.receiver_mobile);
							newAddress.find(".receiver_mobile").text(address.receiver_mobile);
							newAddress.data("receiver-address",address.receiver_address);
							var addressText=$("[value='"+address.province+"']").text()+($("[value='"+address.city+"']").text()=="--不用选啦--" ? "" : $("[value='"+address.city+"']").text())+($("[value='"+address.county+"']").text()=="--不用选啦--" ? "" : $("[value='"+address.county+"']").text())+address.receiver_address;
							newAddress.find(".receiver_address").text(addressText.length>31 ? addressText.substring(31,addressText)+"..." : addressText);
							saveAddressToFrontPage(areaSelected,newAddress);
	                		$("#user_addresses").find(".editAddressesContent").append(newAddress);
	                		newAddress.show();
							if(address.setDefault==1){
								setDefaultAddress(newAddress);
							};
	                		history.back();
	                		$("#loading").hide();
	                		$("#loading").css({opacity:"0"});
	                	}
	                },
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").hide();
	                    $("#loading").css({opacity:"0"});
	                }
				})
			}else{                                           //更新地址操作
				$.ajax({
	                url:"updateReceivingAddress/"+$(".user_Address").eq(myOrder.addressIndex).data("receiver-address-id"),
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
	                	if(response==1){
							var editIndex=$("#user_addresses").find(".user_Address").eq(myOrder.addressIndex);
	                		if(address.setDefault==1){
	                			$("#user_addresses").find(".checked").removeClass("checked");
	                			editIndex.addClass("checked");
	                		};
							editIndex.data("receiver",address.receiver);
							editIndex.find(".receiver").text(address.receiver);
							editIndex.data("receiver-mobile",address.receiver_mobile);
							editIndex.find(".receiver_mobile").text(address.receiver_mobile);
							editIndex.data("receiver-address",address.receiver_address);
							var addressText=$("[value='"+address.province+"']").text()+($("[value='"+address.city+"']").text()=="--不用选啦--" ? "" : $("[value='"+address.city+"']").text())+($("[value='"+address.county+"']").text()=="--不用选啦--" ? "" : $("[value='"+address.county+"']").text())+address.receiver_address;
							editIndex.find(".receiver_address").text(addressText.length>31 ? addressText.substring(31,addressText)+"..." : addressText);
							saveAddressToFrontPage(areaSelected,editIndex);
							if(address.setDefault==1){
								setDefaultAddress($("#user_addresses").find(".user_Address").eq(myOrder.addressIndex));
							};
							history.back();
							$("#loading").hide();
							$("#loading").css({opacity:"0"});
	                	}
	                },
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").hide();
	                    $("#loading").css({opacity:"0"});
	                }
				})
			}
		})

		$("#homeTabs").on("click",".homeTab",function(event){        //选中tab处理
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"),$("#orderContent"),$("#waitOfferContent"));
			$(this).parents("#homeTabs").find(".active").removeClass("active");
			$(this).addClass("active");
			$("#homeTabsFixed").find(".active").removeClass("active");
			$("#homeTabsFixed").find("li").eq($(this).index()).addClass("active");
			var that=this;
			changeTab(that);
		})

		$("#homeTabsFixed").on("click",".homeTab",function(event){    //选中tab处理
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"),$("#orderContent"),$("#waitOfferContent"));
			window.scrollTo(0,0);
			showHide($("#homeTabs"),$("#homeTabsFixed"));
			$(this).parents("#homeTabsFixed").find(".active").removeClass("active");
			$(this).addClass("active");
			$("#homeTabs").find(".active").removeClass("active");
			$("#homeTabs").find("li").eq($(this).index()).addClass("active");
			var that=this;
			changeTab(that);
		})

		$("body").on("click",".keepItOn",function(event){     //关闭弹窗
			event.preventDefault();
			event.stopImmediatePropagation();
			history.back();
		})

		$("#homeContent,#itemList").on("click",".cancleOrder",function(event){   //弹出取消订单弹窗
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			if($(that).parents("li").data("item-status")>0){
				$("#cancleOrder").find(".sureToCancle").data("order-id",$(that).parents("li").find(".order_id").text());
			}else{
				$("#cancleOrder").find(".sureToCancle").data("order-id",$(that).parents("li").find(".requirement_id").text());
			}
			$("#cancleOrder").find(".sureToCancle").data("order-index",$(that).parents("li").index());
			$("#cancleOrder").find(".sureToCancle").data("order-status",$(that).parents("li").data("item-status"));
			$("#cancleOrder").show();
			$("#cancleOrder").animate({"opacity":"1"});
			history.pushState({},"","#cancleOrder");
		})
		$("#cancleOrder").on("click",".sureToCancle",function(event){          //取消订单
			event.preventDefault();
			event.stopImmediatePropagation();
			showHide($("#loading"));
			var that=this;
			if($(that).data("order-status")>0){
				$.ajax({
					url:"cancelOrder/"+$(that).data("order-id"),
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
							$("#orderContent").find(".order").eq($(that).data("order-index")).remove();
							if($("#orderContent").find(".order").length==0){
								$(".catchAttention").css({opacity:"0"});
							}else{
								$(".catchAttention").text($("#orderContent").find(".order").length);
							};
							$("#loading").hide();
							$("#loading").css({opacity:"0"});
							history.back();
							checkOrderNum($("#orderContent"));
							setTimeout(function(){
								if(location.hash=="#itemList"){
									history.back();
								}
							},10);
						}else{
							$("#loading").hide();
							$("#loading").css({opacity:"0"});
							alert("删除失败，请重新操作")
						};
					},
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").hide();
	                    $("#loading").css({opacity:"0"})
	                }
				})
			}else{
				$.ajax({
					url:"deleteRequirement/"+$(that).data("order-id"),
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
							$("#waitOfferContent").find(".order").eq($(that).data("order-index")).css({height:"0",opacity:"0"});
							setTimeout(function(){
								$("#waitOfferContent").find(".order").eq($(that).data("order-index")).remove();
								checkOrderNum($("#waitOfferContent"));
							},200);
							$("#loading").hide();
							$("#loading").css({opacity:"0"});
							history.back();
							setTimeout(function(){
								if(location.hash=="#itemList"){
									history.back();
								}
							},10)
						}else{
							$("#loading").hide();
							$("#loading").css({opacity:"0"});
							alert("删除失败，请重新操作")
						};
					},
	                error: function (request, errorType, errorMessage) {
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                    $("#loading").hide();
	                    $("#loading").css({opacity:"0"});
	                }
				})
			}
		})

		$("#homeContent,#itemList").on("click",".callOp,.checkRefund,.toReceive,.exTime",function(event){    //弹窗处理
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			if($(that).hasClass("callOp")){
				$("#callOp").find(".opTel").text($(that).parents("li").data("item-operator-mobile"));
				$("#callOp").find(".call").find("a").attr({"href":"tel:"+$(that).parents("li").data("item-operator-mobile")});
			};
			if($(that).hasClass("checkRefund")){
				var refund=JSON.parse($(that).parents(".order").data("order-refund")).data;
				var lenOld=$("#checkRefund").find(".refund_detailOuter").find(".refund_detail").length;
				for(var i=lenOld; i<refund.length; i++){
					var newrefund=$("#checkRefund").find(".refund_detailOuter").find(".refund_detail").eq(0).clone();
					$("#checkRefund").find(".refund_detailOuter").prepend(newrefund);
					lenOld++;
				};
				for(var i=lenOld; i>refund.length; i--){
					$("#checkRefund").find(".refund_detailOuter").find(".refund_detail").eq(0).remove();
					lenOld--;
				};
				for(var i=0; i<refund.length; i++){                              //展示退款
					var detail=refund[i].description.split("YeYeTech");
					if(!(refund[i].description.split("YeYeTech")[2]==""||refund[i].description.split("YeYeTech")[2]==undefined)){
						$("#checkRefund").find(".refund_title").eq(i).prev("span").show();
						$("#checkRefund").find(".refund_title").eq(i).show();
						$("#checkRefund").find(".refund_number").eq(i).prev("span").show();
						$("#checkRefund").find(".refund_number").eq(i).show();
						$("#checkRefund").find(".refund_detail").eq(i).find("br").eq(0).show();
						$("#checkRefund").find(".refund_detail").eq(i).find("br").eq(1).show();
						$("#checkRefund").find(".refund_amount").eq(i).text(refund[i].amount/100);
						$("#checkRefund").find(".refund_title").eq(i).text(detail[2]);
						$("#checkRefund").find(".refund_number").eq(i).text(detail[1]);
						$("#checkRefund").find(".refund_description").eq(i).text(detail[0]);
					}else{
						$("#checkRefund").find(".refund_title").eq(i).prev("span").hide();
						$("#checkRefund").find(".refund_title").eq(i).hide();
						$("#checkRefund").find(".refund_number").eq(i).prev("span").hide();
						$("#checkRefund").find(".refund_number").eq(i).hide();
						$("#checkRefund").find(".refund_detail").eq(i).find("br").eq(0).hide();
						$("#checkRefund").find(".refund_detail").eq(i).find("br").eq(1).hide();
						$("#checkRefund").find(".refund_amount").eq(i).text(refund[i].amount/100);
						$("#checkRefund").find(".refund_description").eq(i).text(detail[0]);
					}
				}
			};
			if($(that).hasClass("toReceive")){
				$("#toReceive").find(".sureToReceive").data("order-id",$(that).parents(".order").find(".order_id").text());
				$("#toReceive").find(".sureToReceive").data("order-index",$(that).parents(".order").index());
			}
			$("#"+$(that).attr("class")).show();
			$("#"+$(that).attr("class")).animate({"opacity":"1"});
			history.pushState({},"","#"+$(this).attr("class"));
		})

		$("#toReceive").on("click",".sureToReceive",function(event){         //确认收货
			event.preventDefault();
			event.stopImmediatePropagation();
			var that=this;
			showHide($("#loading"));
			$.ajax({
				url:"/user/commitToHasFinished/"+$(that).data("order-id"),
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
						$("#orderContent").find(".order").eq($(that).data("order-index")).remove();
						checkOrderNum($("#orderContent"));
						history.back();
					}else{
						alert("操作失败，请重新操作")
					};
					$("#loading").hide();
					$("#loading").css({opacity:"0"});
					setTimeout(function(){
						if(location.hash=="#itemList"){
							history.back();
						}
					},10);
				},
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                    $("#loading").hide();
                    $("#loading").css({opacity:"0"})
                }
			})
		})

		$("#sureToPayHeader").on("click",function(event){       //确认支付页下地址选择
			event.preventDefault();
			event.stopImmediatePropagation();
			$("#receiving_addresses").find("div").eq(0).html($("#user_addresses").find(".editAddressesContent").clone());
			$("#receiving_addresses").find("div").eq(0).prepend('<p class="addressDirect">请选择收货地址</p>');
			$("#receiving_addresses").find(".deleteOut").remove();
			showHide($("#receiving_addresses"),$("#sureToPay"));
			document.title="更改收货地址";
			history.pushState({},"","#receiving_addresses");
		})

		$("#receiving_addresses").on("click",".user_Address",function(event){         //选择地址后跳回确认支付页
			event.preventDefault();
			event.stopImmediatePropagation();
			var address=$(this);
			$("#sureToPayHeader").find(".receiver_address").data("receiver-address-id",address.data("receiver-address-id"));
			$("#sureToPayHeader").find(".receiver_name").text(address.data("receiver")+"，");
			$("#sureToPayHeader").find(".receiver_name").parent("p").find("span").eq(0).text("收货人：");
			$("#sureToPayHeader").find(".receiver_mobile").text(address.data("receiver-mobile"));
			$("#sureToPayHeader").find(".receiver_address").text(address.find(".receiver_address").text());
			$("#sureToPayHeader").find(".receiver_address").parent("p").find("span").eq(0).text("收货地址：");
			myOrder.addressSelect=1;
			history.back();
			setTimeout(function(){
				myOrder.addressSelect=0
			},10);
		})

		$("#receiving_addresses").on("click",".addNewAddress",function(event){                //在支付流程中新建地址
			event.preventDefault();
			event.stopImmediatePropagation();
			if($("#user_addresses").find("li").length==7){
				alert("最多添加6个收货地址");
				return false;
			};
			$("#setDefaultAddress").attr({"class":"icon-radio-checked"});
			showHide($("#editUserAddress"),$("#receiving_addresses"));
			history.pushState({},"","#editUserAddress");
		})

		$("#homeContent,#itemList").on("click",".payOrder",function(event){               //确认支付页
			event.preventDefault();
			event.stopImmediatePropagation();
			var address=$("#user_addresses").find(".checked");
			if(address.length==0){
				$("#sureToPayHeader").find(".receiver_name").text("点击此处添加收货地址");
				$("#sureToPayHeader").find(".receiver_name").parent("p").find("span").eq(0).text("");
				$("#sureToPayHeader").find(".receiver_mobile").text("");
				$("#sureToPayHeader").find(".receiver_address").text("");
				$("#sureToPayHeader").find(".receiver_address").parent("p").find("span").eq(0).text("");
			}else{
				$("#sureToPayHeader").find(".receiver_address").data("receiver-address-id",address.data("receiver-address-id"));
				$("#sureToPayHeader").find(".receiver_name").text(address.data("receiver")+"，");
				$("#sureToPayHeader").find(".receiver_name").parent("p").find("span").eq(0).text("收货人：");
				$("#sureToPayHeader").find(".receiver_mobile").text(address.data("receiver-mobile"));
				$("#sureToPayHeader").find(".receiver_address").text(address.find(".receiver_address").text());
				$("#sureToPayHeader").find(".receiver_address").parent("p").find("span").eq(0).text("收货地址：");
			}
			var order=$(this).parents("li");
			var lenItem=JSON.parse(order.data("item-title")).length;
			var lenList=$("#sureToPayContent").find(".itemContainer").length;
			for(var i=lenList; i<lenItem; i++){
				var newItem=$("#sureToPayContent").find(".itemContainer").eq(0).clone();
				$("#sureToPayContent").append(newItem);
				lenList++;
			};
			for(var i=lenList; i>lenItem; i--){
				$("#sureToPayContent").find(".itemContainer").last().remove();
				lenList--;
			};
			for(var i=0; i<lenItem; i++){
				if(JSON.parse(order.data("item-url"))[i].length>0){
					$("#sureToPayContent").find(".itemImage").eq(i).attr({"src":JSON.parse(order.data("item-url"))[i][0]});
				}else{
					$("#sureToPayContent").find(".itemImage").eq(i).attr({"src":"/image/DefaultPicture.jpg"});
				}
				if(JSON.parse(order.data("item-title"))[i].length>30){
					$("#sureToPayContent").find(".itemTitle").eq(i).text(JSON.parse(order.data("item-title"))[i].substr(0,30)+"...");
				}else{
					$("#sureToPayContent").find(".itemTitle").eq(i).text(JSON.parse(order.data("item-title"))[i]);
				}
				$("#sureToPayContent").find(".itemNumber").eq(i).text(JSON.parse(order.data("item-number"))[i]);
				if(!(order.data("item-memos")=="")){
					$("#sureToPayContent").find(".itemContainer").eq(i).data("item-memos",JSON.parse(order.data("item-memos"))[i])
				}else{
					$("#sureToPayContent").find(".itemContainer").eq(i).data("item-memos","");
				}
				$("#sureToPayContent").find(".itemContainer").eq(i).data("item-description",JSON.parse(order.data("item-description"))[i]);
				$("#sureToPayContent").find(".itemContainer").eq(i).data("item-url",JSON.stringify(JSON.parse(order.data("item-url"))[i]));
			}
			$("#payOrderOverview").find(".itemTotalNumber").text(order.find(".totalNumber").text());
			$("#payOrderOverview").find(".orderPrice").text(order.find(".orderPrice").text());
			$("#sureToPayFooter").find(".sureToPay").data("order-id",order.find(".order_id").text())
			$("#sureToPayFooter").find(".sureToPay").data("order-title",order.find(".orderTitle").text());
			setTimeout(showHide($("#sureToPay"),$("#home"),$("#itemList")),500);
			document.title="确认下单";
			history.pushState({},"","#sureToPay");
		})

        $("#sureToPay").on("click",".sureToPay",function(event){                           //拉起支付
        	event.preventDefault();
        	event.stopImmediatePropagation();
        	var that=this;
        	if($("#sureToPayHeader").find(".receiver_address").text()==""){
        		alert("请先添加收货地址");
        		return false
        	}
        	var order={
        		id:$(that).data("order-id"),
        		price:(parseFloat($("#sureToPay").find(".orderPrice").text()))*100,
        		title:$(that).data("order-title")
        	};
            pingpp_one.init({
                app_id:'app_WzDmrT0CC8G0HSen',                 // 该应用在 Ping++ 的应用ID
                order_no:order.id,                           // 这个请替换为订单ID YE开头的
                amount:order.price,                                   // 订单价格，单位：人民币 分
                // 壹收款页面上需要展示的渠道，数组，数组顺序即页面展示出的渠道的顺序；
                // upmp_wap 渠道在微信内部无法使用，若用户未安装银联手机支付控件，则无法调起支付
                channel:['wx_pub'],
                charge_url:"/payment/requestPay",  // 商户服务端创建订单的url
                charge_param:{
                	title:order.title,
                	receiving_address_id:$("#sureToPayHeader").find(".receiver_address").data("receiver-address-id")
                },                       //order title 
                open_id:'olxLuv7ftcxC48-YGe6go_E-0FMo'
            },function(res){
                if(!res.status){
                    alert(res.msg); // 处理错误
                }else{
                	window.location.href="http://www.yeyetech.net/payment/paySuccess";
                    //若微信公众号渠道需要使用壹收款的支付成功页面，则在这里进行成功回调，调用 pingpp_one.success 方法，你也可以自己定义回调函数
                    //其他渠道的处理方法请见第 2 节
                    // pingpp_one.success(function(res){
                    //     if(!res.status){
                    //         alert(res.msg);
                    //     }
                    // },function(){
                    //     //这里处理支付成功页面点击“继续购物”按钮触发的方法，例如：若你需要点击“继续购物”按钮跳转到你的购买页，则在该方法内写入 window.location.href = "你的购买页面 url"
                    //     window.location.href='http://www.yeye.net/user/MyOrder#toNeedSend';//示例
                    // });
                }
            });
        });


		$("#homeContent").on("click",".orderBody",function(event){           //点击需求查看商品列表
			event.preventDefault();
			event.stopImmediatePropagation();
			var order=$(this).parents("li");
			var lenItem=JSON.parse(order.data("item-title")).length;
			var refund;
			if(order.data("item-status")>1){
				refund=JSON.parse(order.data("order-refund")).data;
			};
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
				var checkRefund=false;
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
				if(order.data("item-status")>1){
					for(var j=0; j<refund.length; j++){
						if((refund[j].description.split("YeYeTech")[2]=="")||(refund[j].description.split("YeYeTech")[2]==undefined)||(refund[j].description.split("YeYeTech")[3]==JSON.parse(order.data("item-id"))[i])){
							checkRefund=true;
						}
					};
					if(checkRefund){
						$("#itemList").find(".hasRefund").eq(i).show();
					}else{
						$("#itemList").find(".hasRefund").eq(i).hide();
					};
				}else{
					$("#itemList").find(".hasRefund").eq(i).hide();
				};
				if(!(order.data("item-memos")=="")){
					$("#itemList").find(".itemContainer").eq(i).data("item-memos",JSON.parse(order.data("item-memos"))[i])
				}else{
					$("#itemList").find(".itemContainer").eq(i).data("item-memos","");
				};
				$("#itemList").find(".itemContainer").eq(i).data("item-description",JSON.parse(order.data("item-description"))[i]);
				$("#itemList").find(".itemContainer").eq(i).data("item-url",JSON.stringify(JSON.parse(order.data("item-url"))[i]));
			}
			$("#itemList").find(".itemTotalNumber").text(order.find(".totalNumber").text());
			var index=$("#itemList");
			$("#orderButtonFixed").html(order.clone());
			$("#itemListfooter").find("p").hide();
			$("#itemListfooter").find("p").eq(0).show();			
			if(order.data("item-status")>0){
				$("#itemDetail").find(".descriptionTitle").eq(1).css({opacity:"1"});
				$("#itemDetail").find(".opNote").css({opacity:"1"});
				showHide(index.find(".forWaitPay"),index.find(".forWaitOffer"));
				if(order.data("item-status")>2){
					index.find(".buyerName").text(order.data("item-seller"));
					index.find(".buyerPosition").text(order.find(".country").text());
					index.find("img.forWaitPay").attr({"src":order.data("seller-url")});
				}else{
					index.find(".buyerPosition").text(order.find(".country").text());
					index.find(".buyerName").text("红领巾买手");
					index.find("img.forWaitPay").attr({"src":"/image/seller.jpg"});
				};
				index.find(".orderPriceOuter").css({opacity:"1"});
				index.find(".orderPrice").text(order.find(".orderPrice").text());
				index.find(".idOuter").text("订单号：");
				index.find(".id").text(order.find(".order_id").text());
				$("#logistics").find(".receiving_info").find(".receiver").text(order.data("item-receiver-name")+"，");
				$("#logistics").find(".receiving_info").find(".receiver_mobile").text(order.data("item-receiver-mobile"));
				$("#logistics").find(".receiving_info").find(".receiving_address").text(order.data("item-receiving-address"));
			};
			switch(order.data("item-status")){
				case "0":
					$("#itemDetail").find(".descriptionTitle").eq(1).css({opacity:"0"});
					$("#itemDetail").find(".opNote").css({opacity:"0"});
					showHide(index.find(".forWaitOffer"),index.find(".forWaitPay"));
					index.find(".itemStatus").text("待报价");
					index.find(".orderPriceOuter").css({opacity:"0"});
					index.find(".orderPrice").text("");
					index.find(".idOuter").text("需求号：");
					index.find(".id").text(order.find(".requirement_id").text());
					index.find(".submitTimeOuter").show();
					index.find(".submitTime").text(order.data("item-created-time"));
					$("#logistics").hide();
					document.title="需求详情页"
					break;
				case "1":
					index.find(".itemStatus").text("待付款");
					index.find(".offerTimeOuter").show();
					index.find(".offerTime").text(order.data("order-offer-time"));
					index.find(".exTimeOuter").show();
					index.find(".exTimeInner").text(order.data("order-ex-time"));
					$("#logistics").hide();
					document.title="订单详情"
					break;
				case "2":
					index.find(".itemStatus").text("待发货");
					index.find(".offerTimeOuter").show();
					index.find(".offerTime").text(order.data("order-offer-time"));
					index.find(".payTimeOuter").show();
					index.find(".payTime").text(order.data("order-pay-time"))
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
			showHide($("#itemList"),$("#home"));
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

		if(location.hash=="#toPay"){
			$("#waitPay").trigger("click")                  //去待付款
		}else if(location.hash.substr(0,10)=="#sureToPay"){
			$("#waitPay").trigger("click")                  //去待付款
			hasAnchor = 1;
		}
		else{
		    $.ajax({
		        url:"toggleToNeedToPay",
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
		        	};
					$("#loading").hide();
					$("#loading").animate({"backgroundColor":"rgba(255,255,255,0)"});
					$("#loading").css({opacity:"0"});
		        },
		        error: function (request, errorType, errorMessage) {
		            alert("error:" + errorType + ";  message:" + errorMessage);
					$("#loading").animate({"backgroundColor":"rgba(255,255,255,0)"});
		            $("#loading").css({opacity:"0"});
		            $("#loading").hide();
		        }
		    });
		    if(location.hash=="#toNeedSend"){
				$("#waitSend").trigger("click")             //去待发货
		    }else if(location.hash=="#toSent"){
		    	$("#sent").trigger("click")                 //去已发货
		    }else if(location.hash=="#toCompleted"){         //去已完成
		    	$("#completed").trigger("click")
		    }else{
		    	$("#waitOffer").trigger("click")            //默认tab
		    };
		}
		history.replaceState({},"","#home");
	})