@extends("operation.partial.master")

@section('content')

	<div id="staffDetail">
        <table class="ui-table-order">
            <thead>
                <tr class="header-row">
                    <td colspan="4">
                        <?php $update_url = '/employee/updateLevel/'. $employee->employee_id;
                              if($employee->op_level == 0)
                            {
                                $level = '无权限';
                            }
                            elseif($employee->op_level == 3)
                            {
                                $level = '普通权限';
                            }
                            elseif($employee->op_level == 4)
                            {
                                $level = '高级权限';
                            }
                        ?>
	                    <form method="post" action="{{$update_url}}" style="margin: 2px 0 0 20px;">
				            <input type="hidden" name="_token" value="{{ csrf_token() }}">
				            <input type="hidden" value="opid">
				            <label for="op_level">权限:<span id="level" style="color: red">{{$level}}</span><span class="placeholder">占位</span></label>
                            <?php if($employee->op_level<4){ ?>
		                    <select id="op_level" name="op_level">
		                    	<option value="0">无权限</option>
		                    	<option value="3">普通权限</option>
		                    	<option value="4">高级权限</option>
		                    </select>
		                    <button class="btn btn-xs btn-success" type="submit">保存</button>
                            <?php } ?>
	                    </form>
	                </td>
                </tr>
            </thead>
            <tbody>
                <tr class="separation-row"></tr>
                <tr class="body-row">
                    <td class="clearfix image-cell" rowspan="1">
                        <img src="{{$employee->user->headimgurl}}">
                    </td>
                    <td class="detail1-cell" rowspan="1">
                        <p>
                        	<span>姓名：</span>
                        	<span>{{$employee->real_name}}</span>
                        </p>
                        <p>
                        	<span>身份证号：</span>
                        	<span>{{$employee->identity_card_no}}</span>
                        </p>
                        <p>
							<span>微信号：</span>
                        	<span>{{$employee->user->wx_number}}</span>
                        	<span class="placeholder">占</span>
                        </p>
                    </td>
                    <td class="detail2-cell">
                        <p>
                        	<span>注册手机号：</span>
                        	<span>{{$employee->user->mobile}}</span>
                        </p>
                        <p>
                        	<span>注册邮箱：</span>
                        	<span>{{$employee->user->email}}</span>
                        </p>
                    </td>
                    <td class="detail3-cell">
                    	<p>
                    		<span>生日：</span>
                            <?php $t1 = strtotime($employee->birthday);
                                  $time_show = date('m-d',$t1);
                            ?>
							<span>{{$time_show}}</span>
                    	</p>
                        <p>
                        	<span>注册时间：</span>
                        	<span>{{$employee->created_at}}</span>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php  $activate_url = '/employee/activateEmployee/'. $employee->employee_id;
               $close_url = '/employee/closeEmployee/'. $employee->employee_id;
               $hlj_id = $employee->hlj_id;
               if(count(App\Models\Seller::where('hlj_id',$hlj_id)->first())!=0){
               $seller = App\Models\Seller::where('hlj_id',$hlj_id)->first();
               $free_url = 'employee/freeSeller/'.$seller->seller_id;
               $arrest_url = 'employee/arrestSeller/'.$seller->seller_id;}
               $type = $employee->type;
               switch($type)
               {
                   case '0': $role = '未激活'; break;
                   case '1': $role = '运营'; break;
                   case '2': $role = '开发'; break;
                   case '3': $role = '设计师'; break;
               }
        ?>
        <div class="panel-body">
        	<form style="text-align: left;" method="post" action="{{$activate_url}}">
                <label for="type">类型:<span>{{$role}}</span></label>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <span class="placeholder">占</span>
                <?php if($employee->is_available==0) { ?>
                <select id="type" name="type">
                	<option value="0">未激活</option>
                    <option value="1">运营</option>
                    <option value="2">开发</option>
                    <option value="3">设计师</option>
                </select>
                <button class="btn btn-xs btn-success" type="submit" role="button">激活运营账户</button>
                <?php } ?>
                <?php if($employee->is_available==1) { ?>
            	<a class="btn btn-xs btn-danger" role="button" href="{{$close_url}}">关闭运营账户</a>
                <?php } ?>
                <span class="placeholder">占</span>
                <?php if(count(App\Models\Seller::where('hlj_id',$hlj_id)->first())!=0) { ?>
                <?php if($seller->is_available==0){ ?>
                <a class="btn btn-xs btn-success" role="button" href="{{url($free_url)}}">激活买手账户</a>
                <?php } else { ?>
                <a class="btn btn-xs btn-danger" role="button" href="{{url($arrest_url)}}">关闭买手账户</a>
                <?php } }?>
            </form>
        </div>

    </div>


	<script type="text/javascript">
        $(document).ready(function(){

            $("#staffManagement").addClass("active");

            switch($("#staffDetail").find("#level").text()){
            	case "无权限":
            		$("#op_level").find("option").eq(0).attr({"selected":true});
            		break;
            	case "普通权限":
            		$("#op_level").find("option").eq(1).attr({"selected":true});
            		break;
            	case "高级权限":
            		$("#op_level").find("option").eq(2).attr({"selected":true});
            }

        })
    </script>

@stop