<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">	
	<title>查看物流</title>
</head>
<style type="text/css">
	body{
		padding: 0;
		margin: 0;
		background: #efefef;
		min-height: 100%;
	}
	ul,li{
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.header,.content{
		background: #fff;
		margin-bottom: 10px;
		box-shadow: 0 1px 0 #aaa; 
	}
	.header{
		text-align: center;
		padding-bottom: 1px;
	}
	.imageContainer{
		margin-bottom: 15px;
		padding-top: 15px;
	}
	.imageContainer img{
		width: 50%;
	}
	.infoContainer p {
		margin: 0 0 5px 0;
	}
	.wordContainer{
		color: #a6a6a6;
	}
	.lable{
		color: #f43d66;
	}
	li{
		padding: 1em;
		box-shadow: 0 1px 0 #aaa;
	}
</style>
<body>
	<div class="header">
		<div class="imageContainer"><img src="{{url('image/plane.png')}}"></div>
		<div class="infoContainer">
			<p>您代购的商品买手已从{{$data['country']}}发货，</p>
			<p>请耐心等待。</p>
		</div>
		<div class="wordContainer">
			<p>国际快递可到物流公司官网查询物流信息。</p>
		</div>
	</div>
	<div class="content">
		<ul>
			<li><span class="lable">物流公司：</span><span>{{$data['company']}}</span></li>
			<li><span class="lable">快递单号：</span><span>{{$data['number']}}</span></li>
		</ul>
	</div>
</body>
</html>