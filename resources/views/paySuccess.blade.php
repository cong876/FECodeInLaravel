<!DOCTYPE html>
<html lang="ZH-CN">
<head>
	<meta charset="UTF-8">
	<title>支付成功</title>
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
	<link rel="stylesheet" href={{url("css/style.css")}}>
</head>
<style type="text/css">
	html,body{
		margin: 0;
		padding: 0;
	}
    a, a:hover, a:visited, a:active, a:link{
        text-decoration: none !important;
        color: #9b9b9b;
    }
	#pageseven{
		position: absolute;
		height: 100%;
		width: 100%;
		overflow: hidden;
		background-color: white;
		z-index: 7;
	}
	#contentseven{
		width: 95%;
		text-align: center;
		margin: 1.5em auto 1em auto;
		color: #fa375f;
		padding: 1em 0.5em;
		height: 5em;
	}
	#contentseven div{
		margin: 0 auto;
		width: 1.8rem; 
		height: 1.8rem;
		background-color: #fff; 
		border: 0.1rem solid #fa375f;
		border-radius:1rem;
	}
	#contentseven .icon-checkmark{
		height: 1.8rem; 
		line-height: 1.8rem; 
		display:block; 
		color:#fa375f; 
		text-align:center
	}
	#contentseven p{
		text-align: center;
	}
	#pageseven .onlyDescription{
		color: #fa375f;
		margin: 0;
		padding: 0 2em 2em 2em;
	}
	#footerseven{
		margin-top: 2.5em;
		text-align: center;
	}
	#footerseven .myOrder{
		border: 0.1em solid #9b9b9b;
		color: #9b9b9b;
		font-size: 1.2rem;
		padding: 0.5rem;
	}	
</style>
<body>
	<div id="pageseven">
		<div id="contentseven">
			<div>
         		<span class="icon-checkmark"></span>
    		</div>
			<p>您已成功付款，买手将在7日内发货。
			</p>
		</div>
		<div id="footerseven">
			<span class="myOrder"><a href="/user/MyOrder">返回我的订单</a></span>
		</div>
	</div>
</body>
</html>