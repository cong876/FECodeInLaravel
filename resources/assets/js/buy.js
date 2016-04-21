	window.addEventListener( "load", function() {
	    FastClick.attach( document.body );
	}, false );

	$(document).ready(function(){

	var h=document.body.height;
	var w=document.body.scrollWidth;
	$("#pageone").height(h);
	$("#pageone").find(".pure-u-1-3").height($("#pageone").find(".pure-u-1-3").width());
	$("#pagetwo").height(h);
	$("#pagethree").height(h);
	$("#pagesix").height(h);
	$(".pagetitle").height(h*0.09);
	$(".pictureex").width(w*0.2142);
	$(".chosefile").width(w*0.2142);

	var countryindex=0;
	var countrycheck=0;
	var chosecount=0;
	var codeState=0;
	var isDitui=0;
	
	if(location.hash=="#candy"){
		isDitui=1;
	};
	
	history.replaceState({page: 1},"","#pageone");
	window.onpopstate=function(event){
		if(location.hash=="#pageone"){
			document.title="我要买";
			if($(".itemtitle").children("li").length==1){
				toPageone();
			}else{
				$("#pagefive").show();
				$("#pagefive").addClass("bounceInDown");
				$(".background").show();
				addBlur($("#pagetwo"));
				setTimeout(function(){
					$("#pagefive").removeClass("bounceInDown")
				},1000)
			}
		}else if(location.hash=="#pagetwo"){
	        document.title = '我的需求清单';
			if($(".background").css("display")=="none"){
				$("#pagetwo").show();
				$("#pagethree").addClass("bounceOutLeft");
				numbertotal();
				for(var i=1;i<$("#pagethree").children(".itemdetail").length;i++){
					var index=$("#pagethree").children(".itemdetail").eq(i);
					if(!index.find(".icon-checkmark").parent("div").hasClass("check")){
						$("."+index.attr("id")).remove();
						index.find(".chosefile").removeAttr("id");
						index.find(".picturearea").removeAttr("id");
						numbertotal();
						setTimeout(function(){
							index.remove()
						},500)
					}
				}
				setTimeout(function(){
					$("#pagethree").children(".itemdetail").hide();
					$("#pagethree").hide();
					$("#pagethree").removeClass("bounceOutLeft");
				},500)
			}else{
				closeRegister();
			}
		}
	}

	$("#pageone").on("click",".countryname",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		for(var i=0;i<$(this).parent("div").parent("div").find(".countryimg").length;i++){
			$(this).parent("div").parent("div").find(".countryname").eq(i).show();
			$(this).parent("div").parent("div").find(".countryimg").eq(i)
				.attr({"style":"filter:grayscale(100%);-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%)"})
				.removeClass("checked");						
		};
		$(".countryindex").text($(this).find("p").text());
		$(this).hide();
		$(this).prev("img").attr({"style":"filter:none;-webkit-filter:none;-moz-filter:none;-ms-filter:none;-o-filter:none"})
		   .addClass("checked");
	})

	$("#pageone").on("click",".pagetitle",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
	})

	$("#pageone").on("click",".pagefooter",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
	})	

	$("#pageone").on("click",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		for(var i=0;i<$(this).find(".countryimg").length;i++){
			$(this).find(".countryimg").eq(i)
				.attr({"style":"filter:grayscale(100%);-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%)"})
				.removeClass("checked");
			$(this).find(".countryname").show();
		};
		$(this).find(".countryindex").text("");
	})

	$("#checkmarkouter").on("click",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		if($("#pageone").find(".checked").length==1){															
			switch($(".checked").attr("id")){
				case "HongKong":
					$(".countrychose").html("香港");
					countryindex=1
					break;
				case "Japan":
					$(".countrychose").html("日本");
					countryindex=2
					break;
				case "Korea":
					$(".countrychose").html("韩国");
					countryindex=3
					break;
				case "America":
					$(".countrychose").html("美国");
					countryindex=4
					break;
				case "Australia":
					$(".countrychose").html("澳大利亚");
					countryindex=5
					break;
				case "NewZealand":
					$(".countrychose").html("新西兰");
					countryindex=6
					break;
				case "Germany":
					$(".countrychose").html("德国");
					countryindex=7
					break;
				case "England":
					$(".countrychose").html("英国");
					countryindex=8
					break;
				case "French":
					$(".countrychose").html("法国");
					countryindex=9
					break;
				case "Italy":
					$(".countrychose").html("意大利");
					countryindex=10
					break;
				case "OtherArea":
					$(".countrychose").html("任意国家");
					countryindex=11
					break;
			};
			history.pushState({page: 2},"","#pagetwo");
			$("#pagetwo").show();
			$("#pagetwo").addClass("bounceInDown");
			document.title="我的需求清单";
			setTimeout(function(){
				$("#pageone").hide();
			},800);
			setTimeout(function(){
				$("#pagetwo").removeClass("bounceInDown");
				countrycheck=countryindex;
			},1000)
		}
	})

	$("#cancle").on("click",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$("#pagefour").hide();
		$("#pagefive").addClass("bounceOutUp");
		history.pushState({page: 2},"","#pagetwo");
		document.title="我的需求清单";
		setTimeout(function(){
			$("#pagefive").hide();
			$(".background").hide();
			removeBlur($("#pagetwo"));
			$("#pagefive").removeClass("bounceOutUp");
		},600)
	})

	$("#headerfive").on("click","span",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$("#pagefour").hide();
		$("#pagefive").addClass("bounceOutUp");
		history.pushState({page: 2},"","#pagetwo");
		document.title="我的需求清单";
		setTimeout(function(){
			$("#pagefive").hide();
			$(".background").hide();
			removeBlur($("#pagetwo"));
			$("#pagefive").removeClass("bounceOutUp");
		},600)
	})

	$("#suretochange").on("click",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		toPageone();
	})

	var itemnumber=0;
	$("#additemouter").on("click",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		document.title="添加一件商品";
		$(this).children("span").removeClass("catchAttention");
		$(".guide").hide();
		itemnumber++;
		history.pushState({page: 3},"","#pagethree");
		clonedetail(itemnumber);
		setTimeout(function(){
			$("#pagethree").show();
			$("#pagethree").addClass("bounceInLeft");
			$("#pagethree").height($("#pagethree").height());
		},1);
		setTimeout(function(){
			$("#pagetwo").hide();
			$("#pagethree").removeClass("bounceInLeft");
			$(".itemtitle").append($(".itemtitle").children("li").first().clone());
			$(".itemtitle").children("li").last().attr({"class":"item"+itemnumber,"style":"display:block"});
		},501)
	})

	$("#pagetwo").on("click",".icon-bin",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).hide();
		$(this).next("span").show();
		$(this).next("span").addClass("bounceIn");
		var that=this;
		setTimeout(function(){
			$(that).show();
			$(that).addClass("bounceIn");
			$(that).next("span").hide();
			$(that).next("span").removeClass("bounceIn")
		},1500);
		setTimeout(function(){
			$(that).removeClass("bounceIn")
		},2000)
	})

	var placeholder;                                     //输入区域的默认文字处理
	$("body").on("focus","input,textarea",function(){
		placeholder=$(this).attr("placeholder");
		$(this).attr({"placeholder":""})
	})
	$("body").on("blur","input,textarea",function(){
		$(this).attr({"placeholder":placeholder})
	})

	$("#pagetwo").on("click",".delete",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var that=this;
		$("#"+$(that).parent("p").parent("div").parent("li").attr("class")).remove();
		setTimeout(function(){
			$(that).parent("p").parent("div").parent("li").addClass("animated bounceOutLeft");
		},1);
		setTimeout(function(){
			$(that).parent("p").parent("div").parent("li").remove();
			numbertotal()
		},400);
	})

	$("#pagetwo").on("click",".itemtitle3",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
	})

	$("#pagetwo").on("click",".addtd",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var numberindex=parseInt($(this).prev().children(".numbershow").text());
		numberindex++;
		$(this).prev().children(".numbershow").text(numberindex);
		$("#"+$(this).parents("li").attr("class")).find(".numbershow").text(numberindex); //数量之间联动
		numbertotal()
	})

	$("#pagetwo").on("click",".minustd",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var numberindex=parseInt($(this).next().children(".numbershow").text());
		if(numberindex>1){
			numberindex--;
			$(this).next().children(".numbershow").text(numberindex);
			$("#"+$(this).parents("li").attr("class")).find(".numbershow").text(numberindex);
			numbertotal()
		}
	})

	$("#pagetwo").on("click",".itemtitle2",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
	})

	$("#pagetwo").on("click","li",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).attr({"style":"background-color:#e0e0e0"});
		var that=this;
		setTimeout(function(){
			$(that).attr({"style":"background-color:#fff"});
		},500);
		$("#"+$(this).attr("class")).show();
		$("#"+$(this).attr("class")).find(".picturearea").attr({"id":"picturearea"});
		$("#"+$(this).attr("class")).find(".chosefile").attr({"id":"chosefile"});    //控制图片选择滑动
		history.pushState({page: 3},"","#pagethree");
		setTimeout(function(){
			$("#pagethree").show();
			$("#pagethree").addClass("bounceInLeft");
			$("#pagethree").height($("#pagethree").height());
		},1);
		setTimeout(function(){
			$("#pagetwo").hide();
			$("#pagethree").removeClass("bounceInLeft");
		},501)
	})

	$("#pagetwo").on("click",".submit",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		if(!parseInt($("#totalnumber").text())==0){
			if($(".pagefooter1").children("p").find("span").first().text()!="任意国家"){
				$("#finalnumber").children("p").html("您希望从"+$(".pagefooter1").children("p").find("span").first().text()+"代购"+$("#totalnumber").text()+"件商品");
			}else{
				$("#finalnumber").children("p").html("您希望"+"代购"+$("#totalnumber").text()+"件商品");
			}
			$(".background").show();
			addBlur($("#pagetwo"));
			$("#pagefour").show();
			$("#pagefour").addClass("bounceInDown");
			setTimeout(function(){
				$("#pagefour").removeClass("bounceInDown")
			},1000)
		}
	})

	$("#pagefour").on("click",".icon-cross",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$("#pagefour").addClass("bounceOutUp");
		setTimeout(function(){
			$("#pagefour").removeClass("bounceOutUp");
			$("#pagefour").hide();
			$(".background").hide()
			removeBlur($("#pagetwo"));
		},600)
	})

	$("#pagefour").on("click","#makesure",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$("#loading").show();
		$.ajax({                                 //验证用户是否注册过
			url:"checkPhone",
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
	
	$("#pagethree").on("change",".newtitle",function(event){
		var that=this;
		var newtitle="";
		for(var i=0;i<7;i++){
			if(i<$(that).val().length){
				newtitle=newtitle+$(that).val()[i]
			}
		};
		if($(that).val().length>7){
			newtitle=newtitle+"..."
		}
		$("."+$(that).parent("div").parent("div").attr("id")).find(".itemtitle1").children("p").text(newtitle);
		if(!$(that).val()==" "){
			$(that).parent("div").prev("div").find(".icon-checkmark").parent("div").addClass("check")
		}
		else{
			$(that).parent("div").prev("div").find(".icon-checkmark").parent("div").removeClass("check")
		}
	})

	$("#pagethree").on("click",".addtd",function(event){
		event.preventDefault();
		event.stopImmediatePropagation;
		var numberindex=parseInt($(this).prev().children(".numbershow").text());
		numberindex++;
		$(this).prev().children(".numbershow").text(numberindex);
		$("."+$(this).parents(".itemdetail").attr("id")).find(".numbershow").text(numberindex);
	})

	$("#pagethree").on("click",".minustd",function(event){
		event.preventDefault();
		event.stopImmediatePropagation;
		var numberindex=parseInt($(this).next().children(".numbershow").text());
		if(numberindex>1){
			numberindex--;
			$(this).next().children(".numbershow").text(numberindex);
			$("."+$(this).parents(".itemdetail").attr("id")).find(".numbershow").text(numberindex);
		}
	})

	$("#pagethree").on("click",".saveouter",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).parents(".itemdetail").find(".newtitle").blur();
		$(this).parents(".itemdetail").find(".newtitle").blur();
		var that=this;
		if($(that).parents(".itemdetail").find(".newtitle").val()==""){
			$(that).parents(".itemdetail").find(".newtitle").css({"border":"0.2em solid #fa375f"});
			setTimeout(function(){
				$(that).parents(".itemdetail").find(".newtitle").css({"border":"0.2em solid rgba(255,255,255,0)"});
			},1500);
			return false;
		}
		var that=this;
		$("#pagetwo").show();
		$("#pagethree").addClass("bounceOutLeft");
		setTimeout(function(){
			$("#pagethree").removeClass("bounceOutLeft");
			$("#pagethree").hide();
			$(that).parent("div").parent("div").hide();
			$(that).parent("div").parent("div").find(".picturearea").removeAttr("id");
			$(that).parent("div").parent("div").find(".chosefile").removeAttr("id"); //控制图片滑动
			document.getElementsByClassName("itemtitle")[0].scrollTop=document.getElementsByClassName("itemtitle")[0].offsetHeight;
		},500);
		numbertotal();
		history.back();
	})

	$("#pagethree").on("click",".closeouter",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var that=this;
		$("."+$(that).parent("div").parent("div").attr("id")).remove();
		$("#pagetwo").show();
		$("#pagethree").addClass("bounceOutLeft");
		setTimeout(function(){
			$("#pagethree").removeClass("bounceOutLeft");
			$("#pagethree").hide();
			$(that).parent("div").parent("div").remove();
		},500);
		numbertotal();
		history.back();
	})

	wx.ready(function(){
		$("#pagethree").on("click",".chosefile",function(event){
			var that=this;
			wx.chooseImage({
			    count: 9-$(that).parent("div").find(".pictureshow").length,
			    sizeType: ['original', 'compressed'],
			    sourceType: ['album', 'camera'],
			    success: function (res) {
			        var localIds = res.localIds;
			        var oldLen=$(that).parent("div").find(".pictureshow").length;
					for(var i=0; i<localIds.length; i++){
						var pictureshow=$(that).parent("div").prev("div").clone().attr({"class":"pictureshow"});
						pictureshow.find("img").attr({"src":localIds[i]});
						$(that).before(pictureshow);
						$(that).parent("div").width(($(that).width()+5)*$(that).parent("div").children("div").length);
						document.getElementById("picturearea").scrollLeft=(document.getElementById("picturearea").offsetWidth/4)*($(that).parent("div").children("div").length-4.3);
						$(that).next("i").hide();
						if($(that).parent("div").find(".pictureshow").length==9){
							$(that).hide();
						};
					};
					syncUpload(localIds,oldLen,that);
			    }
			})
		})

		var syncUpload = function(localIds,c,d){
		    var localId = localIds[localIds.length-1];
		    wx.uploadImage({
		        localId: localId,
		        isShowProgressTips: 1,
		        success: function (res) {
		        	localIds.pop();
		            var serverId = res.serverId; // 返回图片的服务器端ID;
		            $(d).parent("div").find(".pictureshow").eq(localIds.length+c).data("src",serverId);
		            if(localIds.length > 0){
		                syncUpload(localIds,c,d);
		            }
		        }
		    });
		};
	})

	$("#pagethree").on("click",".picturedelete",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var that=this;
		$(that).parent("div").addClass("animated bounceOutLeft");
		setTimeout(
			function(){
				if($(that).parents(".picturescroll").children(".pictureshow").length==9){
					$(that).parents(".picturescroll").find(".chosefile").show();
				};
				$(that).parent("div").remove();
				$(that).parent("div").parent("div").width(($(that).parent("div").width()+5)*$(that).parent("div").parent("div").children("div").length);
				var a=document.getElementById("chosefile");
				document.getElementById("picturearea").scrollLeft=a.offsetLeft-w*0.7;
		},400)
	})

	$("#pagethree").on("click",".pictureshow",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).find(".picturedelete").attr({"style":"display: table"});
		var that=this;
		setTimeout(function(){
			$(that).find(".picturedelete").hide();
		},1000)
	})


	$("#pagesix").on("click",".clear",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).prev("inpu").val("");
		$(this).prev("input").focus();
	})

	var patt=/^[0-9]{11}$/;
	var mobileNumber;
	var int;
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
			url:"authMobile/"+data.mobile,                             //验证用户手机是否注册过的url
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
            			url: "getVerifyCode/"+data.mobile,
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
				url: "verifyCode/"+mobileNumber+"/"+$("#captchas").val(),
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
							url:"register",
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
				url:"register",
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

	$("#pageseven").on("click",".myOrder",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		window.location.href="/user/MyOrder"
	})

	var submitItem=function(){
		var items=[];
		for(var i=0;i+1<$("#pagethree").children(".itemdetail").length;i++){
			var iteminfo={
				"order":"",
				"title":"",
				"number":"",
				"pic_urls":[],
				"description":""
				};
			var index=$("#pagethree").children(".itemdetail").eq(i+1);
			iteminfo.order=i;
			iteminfo.title=index.find(".newtitle").val();
			iteminfo.number=index.find(".numbershow").text();
			for(var j=0;j<index.find(".pictureshow").length;j++){
				iteminfo.pic_urls.push(index.find(".pictureshow").eq(j).data("src"));
			};
			iteminfo.description=index.find(".newdescription").val();
			items.push(iteminfo);
		};
	    var item=JSON.stringify(items);
		$.ajax({
			url:isDitui==0 ? "" : "/buyPal/buyPromotion",
			type:"post",
			dataType:"json",
			data:{
				'items':item,
				'country_id':countryindex
			},
	        beforeSend: function (xhr) {
	            var token = $("meta[name=csrf-token]").attr('content');
	            if (token) {
	                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
	            }
	        },
			success:function(response){
				if(response.status == 200){
		            $("#pageseven").show();
		            $("#pageseven").addClass("bounceIn");
		            $("#loading").hide();
		        }else if(response.status == 301){
		        	window.location.href = response.url;
		        }else{
		        	alert(response.status+"未提交成功，请重试");
		            $("#loading").hide();
		        }
	        },
	        error:function(request,errorType,errorMessage){
	            alert("error:"+errorType+";  message:"+errorMessage);
	            $("#loading").hide();
	        }
		})
	}

	var clonedetail=function(n){
		$("#pagethree").append($("#item0").clone());
		$("#pagethree").children("div").last().attr({"id":"item"+n,"style":"display:block"});
		$("#pagethree").children("div").last().find(".picturearea").attr({"id":"picturearea"});
		$("#pagethree").children("div").last().find(".chosefile").attr({"id":"chosefile"});   //控制图片滑动
	}

	function numbertotal(){
		var numbermix=$(".itemtitle").children("li").length;
		var numbertotal=0;
		for(var i=1;i<numbermix;i++){
		numbertotal=numbertotal+parseInt($(".itemtitle").children("li").eq(i).find(".numbershow").text())
		}
		$("#totalnumber").text(numbertotal);
	}
	
	function addBlur(element){
		element.css({"-webkit-filter":"blur(3px)","filter":"blur(3px)"})
	}

	function removeBlur(element){
		element.css({"-webkit-filter":"blur(0)","filter":"blur(0)"})
	}

	function showRegister(){
		$("#pagesix").show();
		$("#wx_image").height($("#wx_image").width());
		$("#pagesix").addClass("bounceInLeft");
		$("#pagesix").height($("#pagesix").height());
		history.pushState({page: 6},"","#pagesix");
		setTimeout(function(){
			$("#pagesix").removeClass("bounceInLeft")
		},500)
	}

	function closeRegister(){
		$("#mobile").val("");
		$("#captchas").val("");
		$("#email").val("");
		$("#password").val("");
		$("#confirmPassword").val("");
		$("#countDown").hide();
		$("#sendcaptchas").show();
		window.clearInterval(int);
		$("#pagesix").addClass("bounceOutLeft");
		setTimeout(function(){
			$("#pagesix").hide();
			$("#pagesix").removeClass("bounceOutLeft")
		},500)		
	}

	function toPageone(){
		$("#pageone").find(".countryimg")
			.attr({"style":"filter:grayscale(100%);-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%)"})
			.removeClass("checked");
		$("#pageone").find(".countryname").show();
		var len=$(".itemtitle").children("li").length;
		for(var i=1;i<len;i++){
			$("#"+$(".itemtitle").children("li").eq(1).attr("class")).remove();
			$(".itemtitle").children("li").eq(1).remove();
		};
		$("#pageone").find(".countryindex").text("");
		$("#pagefive").hide();
		$("#pageone").show();
		$("#pagetwo").addClass("bounceOutUp");
		$("#pagethree").addClass("bounceOutUp");
		$("#pagefour").addClass("bounceOutUp");
		setTimeout(function(){
			$("#pagetwo").removeClass("bounceOutUp");
			$("#pagethree").removeClass("bounceOutUp");
			$("#pagefour").removeClass("bounceOutUp");
			$("#pagetwo").hide();
			$("#pagethree").hide();
			$("#pagefour").hide();
			$(".background").hide();
			removeBlur($("#pagetwo"));
			$("#nationalflag").removeAttr("class");
		},1000)
		numbertotal();
	}

	$("#loading").hide();

	})