<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta content="email=no" name="format-detection" />
    <title>运营注册</title>
    <link href={{url("image/LOGO.ico")}} type="image/x-icon" rel="shortcut icon" />
    <link rel="stylesheet" type="text/css" href="http://7xnzm2.com2.z0.glb.qiniucdn.com/bootstrap.min.css">
    <script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/jquery.min.js"></script>
    <script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/bootstrap.min.js"></script>
</head>
<body>
    <style>

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
            margin-top: 15em;
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

        body {
            padding-bottom: 40px;
            background-color: #eee;
        }

        .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }
        .form-signin .form-signin-heading,
        .form-signin input{
            margin-bottom: 15px;
        }
        #password,#secure_password{
            margin-bottom: 2px;
        }
        .form-signin .form-control {
            position: relative;
            height: auto;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
    </style>

<!-- loading页    -->
    <div id="loading">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
        </div>
    </div>

    <form class="form-signin" method="POST" action="/employee/createEmployee" id="operatorRegister">
        <h2 class="form-signin-heading">请注册运营账户</h2>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <label for="real_name">姓名</label>
        <input type="text" id="real_name" name="real_name" class="form-control" placeholder="请填写您的真实姓名" required>
        <label for="ID">身份证号</label>
        <input type="text" id="ID" name="ID" class="form-control" placeholder="请填写您的身份证号" required>
        <label for="birthday">生日</label>
        <input type="date" id="birthday" name="birthday" class="form-control" placeholder="请填写您的生日" required>
        <label for="mobile">手机号码</label>
        <input type="tel" id="mobile" name="mobile" class="form-control" placeholder="请填写您的手机号码" required>
        <label for="captchas">手机验证码</label>
        <button id="getCaptchas" class="btn btn-lg btn-primary btn-xs" type="button" style="width: 7em">获取验证码</button>
        <button id="countDown" class="btn btn-lg btn-primary btn-xs" type="button" style="width: 7em; display: none">重新获取(50s)</button>
        <label class="hidden">手机验证码</label>
        <input type="tel" id="captchas" name="captchas" class="form-control" placeholder="请填写您收到的验证码" required>
        <label for="email">个人邮箱</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="请填写您的个人邮箱" required>
        <label for="wx_number">微信号</label>
        <input type="text" id="wx_number" name="wx_number" class="form-control" placeholder="请填写您的微信号" required>
        <label for="password">登陆密码</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="请填写您的登陆密码" required>
        <label class="hidden">确认登陆密码</label>
        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="确认登陆密码" required>
        <label for="secure_password">安全密码</label>
        <input type="password" id="secure_password" name="secure_password" class="form-control" placeholder="请填写您的安全密码，用以提现等操作" required>
        <label class="hidden">确认安全密码</label>
        <input type="password" id="confirmSecure_password" name="confirmSecure_password" class="form-control" placeholder="确认安全密码，用以提现等操作" required>
        <label class="checkbox-inline">
            <input type="radio" name="type" id="optionsRadios3" value="3" checked> 
            正式号
        </label>
        <label class="checkbox-inline">
            <input type="radio" name="type" id="optionsRadios4" value="4">
            测试号
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit" id="submit">注册</button>
        <button class="btn btn-lg btn-primary btn-block hidden" type="submit" id="sure">提交</button>
    </form>

    <script type="text/javascript">
        
        var operatorRegister={                                           //全局变量打包
            mobilePatt:/^[0-9]{11}$/,
            emailPatt:/^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/,
            IDPatt:/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
            mobile:0
        }
        var int;

        function isChineseChar(str){   
           var reg = /[\u4E00-\u9FA5\uF900-\uFA2D]/;
           return reg.test(str);
        }

        $(document).ready(function(){
            
            $("#submit").on("click",function(event){
                event.preventDefault();
                var that=this;
                for(var i=0; i<$(that).parents("form").find("input").length; i++){
                    if(($(that).parents("form").find("input").eq(i).val() == "") && (i != 5)){              //验证码不用校验为空
                        alert("请填写您的"+$(that).parents("form").find("input").eq(i).prev("label").text());
                        return false;
                    }
                };
                if(!isChineseChar($("#real_name").val())){
                    alert("请填写中文姓名");
                    return false;
                };
                if(!operatorRegister.IDPatt.test($("#ID").val())){
                    alert("请填写正确的身份证号");
                    return false;
                };
                if($("#password").val()!=$("#confirmPassword").val()){
                    alert("两次填写的密码不一致，请重新填写。");
                    return false;
                }else if($("#secure_password").val()!=$("#confirmSecure_password").val()){
                    alert("两次填写的安全密码不一致，请重新填写。");
                    return false;
                };
                if(!operatorRegister.emailPatt.test($("#email").val())){
                    alert("请输入正确的邮箱");
                    return false
                };
                $("#loading").show();
                $.ajax({
                    url: "/employee/authMail/"+$("#email").val(),
                    type: "get",
                    dataType: "json",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function(response){
                        if(response.status){
                            console.log("邮箱验证成功");
                            if (operatorRegister.isBuyer) {
                                $("#sure").click();
                                return false;
                            };
                            $.ajax({
                                url: "/buyPal/verifyCode/"+operatorRegister.mobile+"/"+$("#captchas").val(),     //检查验证码是否正确
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
                                        console.log("验证码验证成功");
                                        window.clearInterval(int);                              //验证成功
                                        $("#countDown").hide();
                                        $("#getCaptchas").show();
                                        $("#sure").click();
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
                        }else{
                            $("#loading").hide();
                            alert("邮箱已被注册，请更换");
                            return false;
                        }
                    },
                    error:function(request,errorType,errorMessage){
                        $("#loading").hide();
                        alert("error:"+errorType+";  message:"+errorMessage);
                    }
                })
            })
            $("#getCaptchas").on("click",function(event){
                event.preventDefault();
                var that=this;
                var time=50;
                var data={
                    mobile:$("#mobile").val()
                };
                if(!operatorRegister.mobilePatt.test(data.mobile)){
                    alert("请输入正确的手机号");
                    return false
                };
                $("#loading").show();
                $.ajax({
                    url:"/user/authMobile",                             //验证用户手机是否注册过的url
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
                            $("#mobile").attr({"readonly": "readonly"});
                            $("#captchas").removeAttr("required");
                            $("#captchas").attr({"disabled": "disabled"});
                            operatorRegister.isBuyer = true;
                            alert("此手机已经注册过，不用输入验证码了");
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
                                        operatorRegister.mobile=data.mobile;
                                        $("#captchas").val("");
                                        $("#captchas").focus();
                                        $(that).hide();
                                        $("#countDown").show();
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
                                        $("#loading").hide();
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
                        alert("error:"+errorType+";  message:"+errorMessage);
                    }
                })
            })

        })
    </script>

</body>
</html>







