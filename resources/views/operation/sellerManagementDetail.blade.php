@extends("operation.partial.master")

@section('content')

	<div id="sellerDetail">
        <table class="ui-table-order">
            <thead>
                <tr class="header-row">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: center;">
                    	<a href="" data-toggle="modal" data-target="#checkRemarks">查看备注</a>
                    	<span class="placeholder">占位</span>
                    	<a href="" data-toggle="modal" data-target="#addRemarks">添加备注</a>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr class="separation-row"></tr>
                <tr class="body-row">
                    <td class="clearfix image-cell" rowspan="1">
                        <img src="{{!empty($seller->user->headimgurl) ? $seller->user->headimgurl : url('/image/DefaultPicture.jpg')}}">
                    </td>
                    <td class="detail1-cell" rowspan="1">
                        <p>
                        	<span>国家/地区：</span>
                        	<span class="sellerCountry">{{$seller->country->name}}</span>
							<span class="placeholder">占</span>
                        	<a class="btn btn-info btn-xs changeSellerCountry" data-toggle="modal" data-target="#changeSellerCountry">更换国家</a>
                        </p>
                        <p>
                        	<span>真实姓名：</span>
                        	<span class="sellerRealname">{{$seller->real_name}}</span>
                        </p>
                        <p>
                        	<?php if($seller->user->wx_number!=''){ ?>
							<span>微信号：</span>
                        	<span>{{$seller->user->wx_number}}</span>
                        	<span class="placeholder">占</span>
							<?php } else {?>
                        	<a class="btn btn-info btn-xs addWeixinId" data-toggle="modal" data-target="#addWeixinId">添加微信号</a>
							<?php } ?>
                        </p>
                    </td>
                    <td class="detail2-cell">
                        <p>
                        	<span>注册手机号：</span>
							<?php if($seller->user->mobile!=''){
							      $mobile = $seller->user->mobile;
							}else{
								$mobile = 1831132939;
							}
							?>
                        	<span class="sellerMobile">{{$mobile}}</span>
                        </p>
                        <p>
                        	<span>注册邮箱：</span>
                        	<span class="sellerMobile">{{$seller->user->email}}</span>
                        </p>
                        <p>
                        	<span>注册时间：</span>
                        	<span class="sellerRegisterTime">{{$seller->created_at}}</span>
                        </p>
                    </td>
                    <td class="detail3-cell">
                        <p>
	                        <span>买手状态：</span>
							<?php if($seller->is_available==true){
								$state = '正常';}
								else { $state = '小黑屋';}
								?>
	                        <span class="sellerStatus">{{$state}}</span>
	                        <span class="placeholder">占</span>
							<?php if($seller->seller_type==1){ ?>
						    <?php if($seller->is_available==true){ ?>
	                        <a class="btn btn-xs btn-danger block" data-toggle="modal" data-target="#block">关小黑屋</a>
							<?php } else{ ?>
	                        <a class="btn btn-xs btn-success disblock" data-toggle="modal" data-target="#disblock">查看原因</a>
							<?php } } ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
	</div>

	<div class="alert alert-info" role="alert" style="margin-top: 10px">
		<p><strong>接单情况</strong></p>
		<?php
		$memos = DB::table('seller_memos')->where('seller_id',$seller->seller_id)->where('is_available',1)->orderBy('created_at','desc')->get();
		$memoUrl = 'operator/addSellerMemo/'.$seller->seller_id;
		$updateUrl = 'operator/updateSellerCountry/' .$seller->seller_id;
        $arrestUrl = '/operator/arrestSeller/'. $seller->seller_id;
		$addUrl = '/operator/addWX_Number/'. $seller->seller_id;
		$freeUrl = '/operator/freeSeller/' . $seller->seller_id;
		$totalNumber = $seller->seller_refuse_orders_num+$seller->seller_success_orders_num;
		if($seller->seller_success_orders_num==0)
		{
			$zero = 0;
		?>
		<p><span>接单次数：</span><span>{{$seller->seller_receive_orders_num}}</span><span>次</span></p>
		<p><span>拒单次数：</span><span>{{$seller->seller_refuse_orders_num}}</span><span>次</span></p>
		<p><span>成功交易次数：</span><span>{{$zero}}</span><span>次</span></p>
		<p><span>成功交易总额：</span><span>{{$zero}}</span><span>元</span></p>
		<p><span>成功交易均价：</span><span>{{$zero}}</span><span>元</span></p>
		<?php }else{ ?>
		<p><span>接单次数：</span><span>{{$seller->seller_receive_orders_num}}</span><span>次</span></p>
		<p><span>拒单次数：</span><span>{{$seller->seller_refuse_orders_num}}</span><span>次</span></p>
		<p><span>成功交易次数：</span><span>{{$seller->seller_success_orders_num}}</span><span>次</span></p>
		<p><span>成功交易总额：</span><span>{{$seller->seller_success_incoming}}</span><span>元</span></p>
		<p><span>成功交易均价：</span><span>{{sprintf('%.2f',$seller->seller_success_incoming/$seller->seller_success_orders_num)}}</span><span>元</span></p>
		<?php } ?>
	</div>

	<!-- 模态框（Modal） -->
	<div class="modal fade" id="changeSellerCountry" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog" style="width: 400px">
	        <div class="modal-content">
	           	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
	                  &times;
	            	</button>
		            <h4 class="modal-title">
		               更换买手国家前请和买手充分沟通。请慎重处理！
		            </h4>
	        	</div>
		        <div class="modal-body">
		            <form role="form" method="get" class="clearfix" id="sellerCountry" action="{{url($updateUrl)}}">
		                <input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <label for="seller_country">选择国家</label>
		                <div class="form-group">
		                    <select class="form-control input-sm" name="seller_country" class="seller_country" id="seller_country">
		                        <option value="0">--请选择--</option>
								<?php
								$countries = App\Models\Country::get();
								foreach($countries as $country) {
									$country_id = $country->country_id;
									$name = $country->name;
									?>
								<option value="{{$country_id}}">{{$name}}</option>
								<?php } ?>
		                    </select>
		                </div>           
		            </form>
		        </div>
		        <div class="modal-footer" style="text-align: center;">
		            <button type="button" class="btn btn-primary toChange">
		               提交更改
		            </button>
		        </div>
	    	</div>
		</div>
	</div>

	<div class="modal fade" id="addWeixinId" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog" style="width: 400px">
	        <div class="modal-content">
	           	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
	                  &times;
	            	</button>
	            <h4 class="modal-title">
	               给买手添加微信号之前请和买手确认！
	            </h4>
	        	</div>
		        <div class="modal-body">
		            <form role="form" method="post" class="clearfix" id="sellerWeixinId" action="{{url($addUrl)}}">
		                <input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <label for="seller_weixinId">添加微信号:</label>
		                <div class="form-group">
		                    <input class="form-control input-sm" name="seller_weixinId" class="seller_weixinId" id="seller_weixinId" />
		                </div>
		            </form>
		        </div>
		        <div class="modal-footer" style="text-align: center;">
		            <button type="button" class="btn btn-primary toAdd">提交更改</button>
		        </div>
	    	</div>
	    </div>
	</div>

	<div class="modal fade" id="block" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog" style="width: 400px">
	        <div class="modal-content">
	           	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
	                  &times;
	            	</button>
	            <h4 class="modal-title" style="color: red">
	               	<strong>被关小黑屋后，该买手将失去接单资格。请慎重！</strong>
	            </h4>
	        	</div>
		        <div class="modal-body">
		            <form role="form" method="post" class="clearfix" id="blockReason" action="{{url($arrestUrl)}}">
		                <input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <label for="block_reason">请输入拉黑原因:</label>
		                <div class="form-group">
		                    <textarea class="form-control input-sm" name="block_reason" class="block_reason" id="block_reason"></textarea>
		                </div>
		            </form>
		        </div>
		        <div class="modal-footer" style="text-align: center;">
		            <button type="button" class="btn btn-danger toBlock">必须关!!</button>
		        </div>
	    	</div>
	    </div>
	</div>

	<div class="modal fade" id="disblock" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog" style="width: 400px">
	        <div class="modal-content">
	           	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
	                  &times;
	            	</button>
	            <h4 class="modal-title">
	               	买手因为以下原因被关进小黑屋：
	            </h4>
	        	</div>
				<?php if($seller->is_available==false){ ?>
		        <div class="modal-body">
					<?php if($seller->seller_type==1){?>
		            <p>{{$seller->sellerPrison->reasons}}</p>
					<?php } ?>
		        </div>
		        <div class="modal-footer" style="text-align: center;">
		            <a type="button" class="btn btn-success" id="sureToDisBlock" data-href="{{url($freeUrl)}}">拉出小黑屋</a>
		        </div>
				<?php } ?>
	    	</div>
	    </div>
	</div>

	<div class="modal fade" id="addRemarks" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog" style="width: 400px">
	        <div class="modal-content">
	           	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
	                  &times;
	            	</button>
	        	</div>
		        <div class="modal-body">
		            <form role="form" method="post" class="clearfix" id="sellerRemarks" action="{{url($memoUrl)}}">
		                <input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <label for="seller_remarks">填写买手备注:</label>
		                <div class="form-group">
		                    <textarea class="form-control input-sm" name="seller_remarks" class="seller_remarks" id="seller_remarks" required="required"></textarea>
		                </div>
		                <hr />
		                <div style="text-align: center;">
		                	<button type="submit" class="btn btn-primary">提交更改</button>
		                </div>
		            </form>
		        </div>
	    	</div>
	    </div>
	</div>

	<div class="modal fade" id="checkRemarks" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog" style="width: 400px">
	        <div class="modal-content">
	           	<div class="modal-header">
	            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
	                  &times;
	            	</button>
	            <h4 class="modal-title">
	               	买手备注：
	            </h4>
	        	</div>
		        <div class="modal-body" style="max-height: 500px; overflow-y: auto">
		        	<ul>
						@foreach($memos as $memo)
							<?php $hlj_id = $memo->hlj_id;
							$name = App\Models\User::find($hlj_id)->employee->real_name; ?>
							<li class="remarks" data-id="{{$memo->seller_memo_id}}">
								<p>
									<span>备注时间：</span>
									<span>{{$memo->created_at}}</span>
									<span>  备注人：</span>
									<span>{{$name}}</span>
									<span class="placeholder">占位</span>
									<?php if((Auth::user()->hlj_id == $memo->hlj_id)||(Auth::user()->employee->op_level>3)) { ?>
									<button type="button" class="close delete">&times;</button>
									<?php } ?>
								</p>
								<p>{{$memo->content}}</p>
							</li>
						@endforeach
		        	</ul>
		        </div>
	    	</div>
	    </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){

            $("#sellerManagement").addClass("active");

            $("#changeSellerCountry").on("click",".toChange",function(event){    //更改买手国家
            	event.preventDefault();
            	if($("#changeSellerCountry").find("select").val()==0){
            		alert("请选择买手国家");
            		return false;
            	};
            	$("#changeSellerCountry").find("form").submit();
            })

            $("#addWeixinId").on("click",".toAdd",function(event){           //添加买手微信号
            	event.preventDefault();
            	if($("#addWeixinId").find("input").eq(1).val()==""){
            		alert("请填写买手微信号");
           			return false
            	};
            	$("#addWeixinId").find("form").submit();
            })

            $("#block").on("click",".toBlock",function(event){               //拉小黑屋
            	event.preventDefault();
            	if($("#block").find("textarea").val()==""){
            		alert("请填写拉黑原因");
            		return false;
            	};
            	$("#block").find("form").submit();
            })

            $("#sureToDisBlock").on("click",function(event){
            	event.preventDefault();
            	if(confirm("确认将他拉出小黑屋么？")){
            		window.location.href=$(this).data("href")
            	}
            })

        })
    </script>

@stop