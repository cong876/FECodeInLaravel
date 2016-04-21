	AV.initialize("k1cslg2apm1r2onvapnh5elh8wiqooc5u75d23e4jiltmiyf", "zi83n282qzi93sepg6wzlx7jrk5e79ph4219snepgxfiurcv");	

	$(document).ready(function(){

	var h=document.body.scrollHeight;
	var w=document.body.scrollWidth;
	$("#pageone").height(h);
	$("#pagetwo").height(h);
	$("#pagethree").height(h);
	$("#pagesix").height(h);
	$(".pagetitle").height(h*0.09);
	$(".pictureshow").width(w*0.2142);
	$(".chosefile").width(w*0.2142);
	$(".picturescroll").width(w*0.2142*$(".pictureshow").length+5*$(".pictureshow").length);
		
	var countryindex=0;
	var countrycheck=0;
	var chosecount=0;
	var codeState=0;
	history.replaceState({page: 1},"","#pagetwo");
	window.onpopstate=function(event){
		if(location.hash=="#pagetwo"){
			if($(".background").css("display")=="none"){
				$("#pagetwo").show();
				$("#pagethree").addClass("bounceOutLeft");
				for(var i=1;i<$("#pagethree").children(".itemdetail").length;i++){
					var index=$("#pagethree").children(".itemdetail").eq(i);
					if(!index.find(".icon-checkmark").parent("div").hasClass("check")){
						$("."+index.attr("id")).remove();
						index.find(".chosefile").removeAttr("id");
						index.find(".picturearea").removeAttr("id");
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

	$("#pagetwo").on("tap","li",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).attr({"style":"background-color:#e0e0e0"});
		var that=this;
		setTimeout(function(){
			$(that).attr({"style":"background-color:#fff"});
		},500);
		$("#"+$(this).attr("class")).show();
		history.pushState({page: 3},"","#pagethree");
		setTimeout(function(){
			$("#pagethree").show();
			$("#pagethree").addClass("bounceInLeft");					
		},1);
		setTimeout(function(){
			$("#pagetwo").hide();
			$("#pagethree").removeClass("bounceInLeft");
		},501)								
	})

	$("#pagetwo").on("tap",".submit",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(".background").show();
		addBlur($("#pagetwo"));
		$("#pagefour").show();
		$("#pagefour").addClass("bounceInDown");
		setTimeout(function(){
			$("#pagefour").removeClass("bounceInDown")
		},1000)
	})

	$("#pagefour").on("tap",".icon-cross",function(event){
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

	$("#pagefour").on("tap","#makesure",function(event){
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
							iteminfo.pic_urls.push(index.find(".pictureshow").eq(j).attr("src"));
						};
						iteminfo.description=index.find(".newdescription").val();
						items.push(iteminfo);
					};
				    var item=JSON.stringify(items);
					$.ajax({
						url:"",
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
				            $("#pageseven").find(".country").text($(".countrychose").text());
				            $("#pageseven").find(".number").text($("#totalnumber").text());
				            $("#pageseven").show();
				            $("#pageseven").addClass("bounceIn");
				            $("#loading").hide();
				        },
				        error:function(request,errorType,errorMessage){
				            alert("error:"+errorType+";  message:"+errorMessage);
				            $("#loading").hide();
				        }
					})
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

	$("#pagethree").on("tap",".saveouter",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		$(this).parents(".itemdetail").find(".newtitle").blur();
		$(this).parents(".itemdetail").find(".newtitle").blur();
		var that=this;
		$("#pagetwo").show();
		$("#pagethree").addClass("bounceOutLeft");
		history.back();
	})

	var patt=/^[0-9]{11}$/;
	var mobileNumber;
	var int;
	$("#sendcaptchas").tap(function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var data={
			mobile:$("#mobile").val()
		}
		if(!patt.test(data.mobile)){
			alert("请输入正确的手机号");
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
            		alert("此手机已经注册过，请更换手机号码");
            	}else{
					AV.Cloud.requestSmsCode({                                 //发送验证码
							mobilePhoneNumber:data.mobile,
							name: '红领巾',
							op: '手机号绑定',
							ttl: 5
					}).then(function(){
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
					}, function(err){
						$("#loading").hide();
						alert("发送失败")//发送失败
					});
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
	
	$("#pagesix").on("tap","#register",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();	
		var userinfo={
			mobile:"",
			email:""
		}
		userinfo.mobile=mobileNumber;
		userinfo.email=$("#email").val();
		if(!mobileNumber){
			alert("请输入正确的手机号并验证");
			return false;
		};
		if(!emailepatt.test(userinfo.email)){
			alert("请输入正确的邮箱");
			return false;
		};
		if($("#captchas").val()==""){
			alert("请输入验证码");
			return false;
		};
		if(codeState==0){
			$("#loading").show;
			AV.Cloud.verifySmsCode($("#captchas").val(), $("#mobile").val()).then(function(){										
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
							$("#loading").hide();					
							$("#testRegister").hide();
							$("#pagesix").addClass("bounceOutLeft");
							setTimeout(function(){
								$("#pagesix").hide();
								$("#pagesix").removeClass("bounceOutLeft")
							},500)
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
			}, function(err){
				$("#loading").hide();
				alert("验证码错误，请重新输入");                        //验证失败
			});
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
						$("#loading").hide();					
						$("#testRegister").hide();
						$("#pagesix").addClass("bounceOutLeft");
						setTimeout(function(){
							$("#pagesix").hide();
							$("#pagesix").removeClass("bounceOutLeft")
						},500)
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

	$("#pageseven").on("tap",".goShopping",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		location.reload();
	})

	$("#pageseven").on("tap",".MyOrder",function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		history.replaceState({},"","/buyPal/buy");
		window.location="/user/MyOrder";
	})
	
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

	$("#loading").hide();

	})