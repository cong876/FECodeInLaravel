@extends("operation.partial.master")

@section('content')

	<div id="buyerDetail">
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
                        <img src="{{ !empty($buyer->user->headimgurl) ? $buyer->user->headimgurl : url('/image/DefaultPicture.jpg') }}">
                    </td>
                    <td class="detail1-cell" rowspan="1">
                        <p>
                        	<span>昵称：</span>
                        	<span class="sellerRealname">{{$buyer->user->nickname}}</span>
                        </p>
                        <p>
                        	<?php if($buyer->user->wx_number!='') { ?>
							<span>微信号：</span>
                        	<span>{{$buyer->user->wx_number}}</span>
                        	<span class="placeholder">占</span>
							<?php } else {  ?>
                        	<a class="btn btn-info btn-xs addWeixinId" data-toggle="modal" data-target="#addWeixinId">添加微信号</a>
							<?php } ?>
                        </p>
                    </td>
                    <td class="detail2-cell">
                        <p>
                        	<span>注册手机号：</span>
                        	<span class="sellerMobile">{{$buyer->user->mobile}}</span>
                        </p>
                        <p>
                        	<span>注册邮箱：</span>
                        	<span class="sellerMobile">{{$buyer->user->email}}</span>
                        </p>
                    </td>
                    <td class="detail3-cell">
                        <p>
                        	<span>注册时间：</span>
                        	<span class="sellerRegisterTime">{{$buyer->created_at}}</span>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
	</div>

	<div class="alert alert-info" role="alert" style="margin-top: 10px">
		<p><strong>购物情况</strong></p>
		<p><span>需求次数：</span><span>{{$buyer->buyer_requirements_num}}</span><span>次</span></p>
		<p><span>购买次数：</span><span>{{$buyer->buyer_success_orders_num}}</span><span>次</span></p>
		<p><span>成交总额：</span><span>{{sprintf('%.2f',$buyer->buyer_actual_paid)}}</span><span>元</span></p>
		<?php if($buyer->buyer_success_orders_num==0){
			$price = 0;
			}else{
			$price = $buyer->buyer_actual_paid/$buyer->buyer_success_orders_num;
		}?>
		<p><span>成交均价：</span><span>{{sprintf('%.2f',$price)}}</span><span>元</span></p>
	</div>

	<!-- 模态框（Modal） -->
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
					<?php $url = 'operator/addBuyerWX_Number/'.$buyer->buyer_id; ?>
		            <form role="form" method="post" class="clearfix" id="buyerWeixinId" action="{{url($url)}}">
		                <input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <label for="buyer_weixinId">添加微信号:</label>
		                <div class="form-group">
		                    <input class="form-control input-sm" name="buyer_weixinId" class="seller_weixinId" id="buyer_weixinId" />
		                </div>
		            </form>
		        </div>
		        <div class="modal-footer" style="text-align: center;">
		            <button type="button" class="btn btn-primary toAdd">提交更改</button>
		        </div>
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
					<?php
					$memos = DB::table('buyer_memos')->where('buyer_id',$buyer->buyer_id)->where('is_available',1)->orderBy('created_at','desc')->get();
					$memoUrl = 'operator/addBuyerMemo/' . $buyer->buyer_id ?>
		            <form role="form" method="post" class="clearfix" id="sellerRemarks" action="{{url($memoUrl)}}">
		                <input type="hidden" name="_token" value="{{ csrf_token() }}">
		                <label for="seller_remarks">填写买家备注:</label>
		                <div class="form-group">
		                    <textarea class="form-control input-sm" name="seller_remarks" class="seller_remarks" id="seller_remarks" required="required"></textarea>
		                </div>
		                <hr />
		                <div class="form-group" style="text-align: center;">
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
	               	买家备注：
	            </h4>
	        	</div>
		        <div class="modal-body" style="max-height: 500px; overflow-y: auto">
		        	<ul>
						@foreach($memos as $memo)
							<?php $hlj_id = $memo->hlj_id;
							$name = App\Models\User::find($hlj_id)->employee->real_name;
							?>
							<li class="remarks" data-id="{{$memo->buyer_memo_id}}">
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

            $("#buyerManagement").addClass("active");

            $("#addWeixinId").on("click",".toAdd",function(event){      //添加买家微信号
                event.preventDefault();
                if($("#addWeixinId").find("input").eq(1).val()==""){
                    alert("请填写买家微信号");
                    return false
                };
                $("#addWeixinId").find("form").submit();
            })

        })
    </script>

@stop