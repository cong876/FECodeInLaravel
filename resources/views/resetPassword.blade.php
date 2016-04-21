<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>重置安全密码</title>
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href={{url("css/style.css")}}>
</head>
<style type="text/css">
	body{
		user-select: none;
		-webkit-user-select: none;
	}
	ul{
		margin: 0;
		padding: 0;
		border: none;
	}
	li{
		list-style: none;
		margin: 0;
		padding: 0;
	}
/*loading页样式*/
	#loading{
		position: fixed;
		width: 100%;
		height: 100%;
		background-color: rgba(255,255,255,0);
		top: 0;
		text-align: center;
		z-index: 1000;
		display: none;
	}
	#loading .spinner{
		margin-top: 18em;
	}
	.spinner {
	  	margin: 100px auto 0;
	  	width: 150px;
	  	text-align: center;
	}
	 
	.spinner > div {
	  	width: 30px;
	  	height: 30px;
	  	background-color: rgb(194,63,92);
	 	border-radius: 100%;
	  	display: inline-block;
	  	-webkit-animation: bouncedelay 1s infinite ease-in-out;
	  	animation: bouncedelay 1s infinite ease-in-out;
	  	-webkit-animation-fill-mode: both;
	  	animation-fill-mode: both;
	}
	 
	.spinner .bounce1 {
	  	-webkit-animation-delay: -0.32s;
	  	animation-delay: -0.32s;
	}
	 
	.spinner .bounce2 {
	  	-webkit-animation-delay: -0.16s;
	  	animation-delay: -0.16s;
	}
	 
	@-webkit-keyframes bouncedelay {
	  	0%, 80%, 100% { -webkit-transform: scale(0.0) }
	  	40% { -webkit-transform: scale(1.0) }
	}
	 
	@keyframes bouncedelay {
	  	0%, 80%, 100% { 
	    	transform: scale(0.0);
	    	-webkit-transform: scale(0.0);
	  	} 40% { 
	    	transform: scale(1.0);
	    	-webkit-transform: scale(1.0);
	  	}
	}	
	#register input{
		border: 1px solid rgba(255,255,255,0);
	}
	#register{
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		overflow: hidden;
		background: -webkit-linear-gradient(top right, rgba(157,58,86,1), rgba(189,64,94,1),rgba(218,73,102,1),rgba(235,85,105,1),rgba(247,108,112,1),rgba(251,143,133,1),rgba(253,197,182,1),rgba(253,214,202,1));
		z-index: 1;
	}
	#register .header{
		height: 24%;
		width: 100%;
		text-align: center;
	}
	#register .header span{
		display: block;
		float: right;
		padding: 0.5em 0.5em 0em 1em;
		opacity: 0;		
	}
	#register .header .placeholder{
		float: left;
		opacity: 0;
	}
	#register .header .user_info{
		width: 100%; 
		height: 80%; 
		text-align: center; 
		padding-top: 2em
	}
	@media all and (min-width: 320px) {
		#wx_image{    
			width: 20.9%; 
			background-color: gray; 
			border-radius: 0.5em;
			margin-top: 1.5em; 
			overflow: hidden;
		}
		#captchas{
			width: 45%;
			padding-right: 0;
		}
		#email{
			width: 85%;
		}				
	}
	@media all and (min-width: 375px) {
		#wx_image{    
			width: 20.9%; 
			background-color: gray; 
			border-radius: 0.5em;
			margin-top: 2em; 
			overflow: hidden;
		}
		#captchas{
			width: 50%;
		}
		#email{
			width: 90%;
		}			
	}		
	#wx_nickname{
		color: white; 
		font-size: 1.1rem;
		margin-top: 0.4em;
		fond-weight: 500;
	}
	#register .content{
		width: 100%;
		height: 76%;
	}
	#register .content form{
		width: 75%;
		margin: 0.5em auto;
		color: #fa375f;		
	}		
	#register .content form input{
		background-color:transparent; 
		box-shadow: none; 
		color: white; 
		padding: 0.2em;
		margin: 0;
		display: inline; 
	}
	#register .content form select{
		width: 100%;
		height: 1.7em;
		background-color:transparent !important; 
		box-shadow: none; 
		color: rgb(252,183,199); 
		padding: 0;
		margin: 0;
		display: inline;
		border: 1px solid rgba(255,255,255,0) !important; 
	}
	#register .content form input::-webkit-input-placeholder{ 
    	color: rgb(252,183,199);
	}
	.clear{
		display: inline-block;
		float: right;
		color: rgb(252,183,199);
    	font-size: 1em;
	}
	#register1 .clear{
		margin-top: 0.33em;
	}
	#register2 .clear{
		color: rgb(252,183,199);
		margin-top: 0.1em;
	}
	#country{
		width: 80%;
	}
	#register .showRight{
		display: inline-block;
		float: right;
		color: rgb(252,183,199);
		padding: 0.2em;
		text-align: center;
		font-size: 0.9em;
		margin-top: 0.1em;
		font-weight: bolder;		
	}
	#sendcaptchas{
		display: inline-block; 
		float: right; 
		color: white;
		background-color: rgba(255,255,255,0.3);
		padding: 0.2em;
		width: 5.4em;
		text-align: center;
		font-size: 0.9em;
		margin-top: 0.15em;
	}
	#countDown{
		display: none; 
		float: right; 
		color: white;
		background-color: rgba(255,255,255,0.3);
		padding: 0.2em;
		width: 5.4em;
		text-align: center;
		font-size: 0.9em;
		margin-top: 0.15em;	
	}
	#mailMark{
		position: relative;
		top: 0.1em;
	}
	#nextStepOuter,#submitOuter{
		width: 100%; 
		height: 2em; 
		margin-top: 1em;
		display:table; 
		text-align: center; 
		background-color: white
	}
	#nextStep,#submit{
		display: table-cell; 
		color: rgb(240,88,104);
		vertical-align: middle;
		padding-top: 0.7em;
		padding-bottom: 0.7em		
	}

	#choseCountry{
		display: none;
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		overflow: hidden;
		background: -webkit-linear-gradient(top right, rgba(157,58,86,1), rgba(189,64,94,1),rgba(218,73,102,1),rgba(235,85,105,1),rgba(247,108,112,1),rgba(251,143,133,1),rgba(253,197,182,1),rgba(253,214,202,1));
		z-index: 2;		
	}
	#choseCountry li{
		color: rgb(255,255,255);
		height: 1em;
		padding: 0.5em;
		width: 100%;
		border-bottom: 1px solid rgb(255,255,255);
	}

	#register2{
		display: none;
	}
	#register2 input{
		width: 82%;
	}
	#register2 fieldset span{
		color: rgb(255,255,255);
		position: relative;
		top: 0.2em;
	}
</style>
<body>

<!-- loading页	 -->
	<div id="loading">
		<div class="spinner">
  			<div class="bounce1"></div>
  			<div class="bounce2"></div>
		</div>
	</div>

	<div class="pure-g" id="register">
		<div class="pure-u-1 header">
			<span class="icon-cross"></span>
			<span class="icon-plus placeholder"></span>
			<?php $user = Auth::user();?>
			<img src={{!empty($user->headimgurl)? $user->headimgurl :url("image/DefaultPicture.jpg")}} id="wx_image">
			<p id="wx_nickname">{{!empty($user->nickname)? $user->nickname : "红领巾买手"}}</p>
		</div>
		<div class="pure-u-1 content" id="register1">
			<form class="pure-form pure-form-aligned">
				<fieldset>
					<legend style="margin-bottom: 0em">
						<input id="countryCode" name="countryCode" type="tel" placeholder="请输入国家/地区代码">
					</legend>
					<legend style="margin-bottom: 0em">
						<input id="mobile" type="tel" name="mobile" style="width: 90%" placeholder="请输入您的手机号"/>
						<span class="icon-cross clear"></span>
					</legend>
					<legend style="margin-bottom: 0.5em">
						<input id="captchas" type="tel" name="captchas" placeholder="请输入验证码"/>
						<span id="sendcaptchas">获取验证码</span>
						<span id="countDown"></span>
					</legend>
				</fieldset>
				<div id="nextStepOuter">
					<span id="nextStep">下一步</span>
				</div>
			</form>
		</div>
		<div class="pure-u-1 content" id="register2">
			<form class="pure-form pure-form-aligned">
				<fieldset>
					<legend style="margin-bottom: 0em">
						<span class="icon-lock"></span>
						<input type="password" id="password" name="password" placeholder="请输入新的安全密码"/>
					</legend>
					<legend>
						<span class="icon-key"></span>
						<input type="password" id="repassword" placeholder="请确认安全密码">
					</legend>
				</fieldset>
				<div id="submitOuter">
					<span id="submit">提交</span>
				</div>
			</form>
		</div>
	</div>

	<script src={{url("js/zepto.min.js")}}></script>
	<script type="text/javascript">
		if (window.location != window.parent.location) window.parent.location = window.location;
		
		$("#register").height($("#register").height());
		$("#wx_image").height($("#wx_image").width());


		var register={                                           //全局变量打包
			mobilePatt:/^[0-9]\d*$/,
			codePatt:/^[0-9]\d*$/,
			mobile:0,
			code:0,
			placeholder:""
		}

		var clearZero=function(num){
			if(num[0]==0){
				num=num.substring(1,num.length); 
				return clearZero(num)
			}else{
				return num
			}
		}

        function isChineseChar(str){   
           var reg = /[\u4E00-\u9FA5\uF900-\uFA2D]/;
           return reg.test(str);
        }

		$(document).ready(function(){

			window.onpopstate=function(){
				if(location.hash=="#register"){
					$("#register2").hide();
					$("#register").show();
					$("#register1").show();
				}
			}

			$("body").on("focus","input,textarea",function(){
				register.placeholder=$(this).attr("placeholder");
				$(this).attr({"placeholder":""})
			})
			$("body").on("blur","input,textarea",function(){
				$(this).attr({"placeholder":register.placeholder})
			})

			$("#sendcaptchas").on("tap",function(event){            //发送验证码操作
				event.preventDefault();
				event.stopImmediatePropagation();
				var data={
					code: clearZero($("#countryCode").val()),
					mobile: clearZero($("#mobile").val())
				};
				if(!register.mobilePatt.test($("#mobile").val())){
					alert("请输入正确的手机号");
					return false
				};
				if(!register.codePatt.test($("#countryCode").val())){
					alert("请填写您所在国家/地区代码(如中国:86)");
					return false
				}
				var that=this;
				var time=50;
				$("#loading").show();
				$.ajax({
					url:"/seller/verifyMobileNumber/"+data.mobile,                             //验证用户手机是否注册过的url
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
		            		alert("手机号码不正确，请填写注册手机号。")
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
						        		register.code=data.code;
										register.mobile=data.mobile;
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
													$("#sendcaptchas").attr({"style":"display:inline-block"});
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
		            		})
		            	}
		        	},
		        	error:function(request,errorType,errorMessage){
		        		$("#loading").hide();
		            	alert("error:"+errorType+";  message:"+errorMessage);
		        	}			
				})			
			})

			$("#register").on("click",".clear",function(event){
				event.preventDefault();
				event.stopImmediatePropagation();
				$(this).prev("inpu").val("");
				$(this).prev("input").focus();
			})

			$("#nextStepOuter").on("tap",function(event){
				event.preventDefault();
				event.stopImmediatePropagation();
				data={
					mobile:0,
					code:0
				};
				data.code=register.code;
				data.mobile=register.mobile;
				if(!register.mobilePatt.test($("#mobile").val())){
					alert("请输入正确的手机号码");
					return false;
				}else{
					if($("#captchas").val()==""){
						alert("请输入验证码");
						return false;
					};
					$("#loading").show();
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
								$("#sendcaptchas").show();
								$("#sendcaptchas").attr({"style":"display:inline-block"});
								$("#register1").hide();
								$("#register2").show();
								$("#loading").hide();
								history.pushState({},"","#register2")
							}else{
								$("#loading").hide();
								alert("验证码错误，请重新输入");                        //验证失败
							}
						}
					});
				}
			})

			$("#submitOuter").on("tap",function(event){
				event.preventDefault();
				event.stopImmediatePropagation();
				if($("#password").val()==""){
					alert("请输入安全密码。");
					return false;
				}
				if($("#password").val()!=$("#repassword").val()){
					alert("请确认两次输入的密码一致");
					return false;
				};
				var data={
						password: $("#password").val()
				};
				$("#loading").show();
				$.ajax({
					url:"/seller/resetPassword",
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
							$("#loading").hide();
							alert("重置成功");
							window.location.href="/seller/management"
						}
					},
	                error: function (request, errorType, errorMessage) {
	                	$("#loading").hide();
	                    alert("error:" + errorType + ";  message:" + errorMessage);
	                }				
				})					
			})
		})
	</script>

</body>
</html>