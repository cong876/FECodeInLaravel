@extends("operation.partial.master")




@section('content')
	@include('operation.partial.activitiesQuery')
	@include('operation.partial.activitiesPartial')

    <div id="myTabContent" class="tab-content">
        <div id="activities" class="panel panel-default tab-pane fade in active">
            <div class="detail panel-body">
                <table class="ui-table-order">
                    <thead>
                        <tr>
                            <th>标题</th>
                            <th>起止日期</th>
                            <th>编辑人员</th>
                            <th>活动类型</th>
                            <th><a class="btn-xs btn-success" role="button" style="padding: 5px"
                            	data-toggle="modal"
                                data-target="#addActivity">新建活动页面</a></th>
                        </tr>
                    </thead>
                   @foreach($activities as $activity)
                        <?php $name = App\Models\Employee::find($activity->publisher_id)->real_name;
                        if($activity->activity_type == 1)
                        {
                            $type = '周期性活动';
                        }
                        elseif($activity->activity_type == 2)
                        {
                            $type = '主题性活动';
                        }
                            $delete_url = '/operator/deleteActivity/'.$activity->activity_id;
                            $detail_url = '/operator/activitiesManagementDetail/'.$activity->activity_id;
                        ?>
                    <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td colspan="4" style="text-align: left; padding-left: 1em;color: blue;font-weight: bold"><em>NO</em>:{{$activity->activity_id}}<span class="placeholder">0000</span>
                                <?php if($activity->is_available == 1){ ?>
                                <span class="label label-danger">已发布</span>
                                <?php }
                                if($activity->activity_type == 2) {
                                    $theme_url = url('app/wx?#/activity/'.$activity->activity_id);
                                ?>
                                <input type="text" style="display: inline-block; float: right" value="{{$theme_url}}">
                                <?php } ?>
                            </td>
                            <td><a data-deleteurl="{{url($delete_url)}}" class="deleteActivity" href="">删除</a></td>
                        </tr>
                        <tr class="body-row">
                            <td class="activitiesTitle-cell" rowspan="1" style="text-align: left">
                                <p class="activityTitle"><img src="{{$activity->pic_urls}}" style="height: 80px;width: 80px;margin: 5px 10px;">{{$activity->activity_title}}</p>
                            </td>
                            <td class="activitiesTime-cell" rowspan="1">
                                <p class="country">{{$activity->activity_start_time}}——{{$activity->activity_due_time}}</p>
                            </td>
                            <td class="activitiesEditor-cell" rowspan="1">
                                <p>{{$name}}</p>
                            </td>
                            <td class="activitiesStyle-cell" rowspan="1">
                                <p>{{$type}}</p>
                            </td>
                            <td class="activitiesDetail-cell" rowspan="1">
                                <div class="td-cont">
                                    <a class="btn btn-primary edit" href="{{url($detail_url)}}" role="button">查看详情</a>
                                    <a class="btn btn-success edit" href="{{url('/operator/refreshActivity/'.$activity->activity_id)}}" role="button">刷新活动</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                   @endforeach
                </table>
            </div>
        </div>

        <div class="modal fade" id="addActivity" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width: 400px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                          &times;
                        </button>
                        <h4 class="modal-title">
                            新建活动：
                        </h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <form role="form" method="post" class="clearfix" action="{{url('operator/createActivity')}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label>请选择活动类型</label>
                                <select name="activityType" id="activityType" class="form-control">
                                	<option value="1">周期性活动</option>
                                	<option value="2">主题性活动</option>
                                </select>
                                <hr>
                                <label>请选择活动日期</label><br>
                                <input type="Date" name="startTime" id="startTime" class="form-control" style="display: inline-block; width: 45%" required> 至
                                <input type="Date" name="endTime" id="endTime" class="form-control" style="display: inline-block; width: 45%" readonly required>
                            	<hr>
                            </div>
                            <div style="text-align: center"><button type="submit" class="btn btn-default">提交</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>		

    </div>
    <nav>
        {!! $activities->appends(Input::query())->render()!!}
    </nav>
    <script type="text/javascript">
            $("#activitiesManagement").addClass("active");
            var indexPage = location.href.split("/")[4].split("?")[0];
            console.log(indexPage);
            switch(indexPage){
                case "allActivities":
                    $("#activitiesAll").addClass("active");
                    break; 
                case "allPeriodActivities":
                    $("#cyclicActivity").addClass("active");
                    break;
                case "allSubjectActivities":
                    $("#themeActivity").addClass("active");
                    break;
            }
            $(".deleteActivity").on("click",function(event){
            	event.preventDefault();
                console.log($(this).data("deleteurl"));
                var that = this;
            	if(confirm("确定要删除"+$(that).parents("tbody").find(".activityTitle").text())){
            		window.location=$(that).data("deleteurl");
            	}
            });
            var doubleNumber = function (foo) {
                foo = foo.toString();
                return foo.length === 1 ? "0"+foo : foo;
            };
            $("#activityType").on("change",function (event) {
                event.preventDefault();
                if($(this).val()==1){
                    $("#endTime").attr({"readonly":"readonly"})
                }else{
                    $("#endTime").removeAttr("readonly");
                }
            });
            $("#startTime").on("change",function (event) {
                event.preventDefault();
                var dateToday = new Date();
                dateToday.setTime(Date.parse($("#startTime").val())+86400000);
                $("#endTime").val(dateToday.getFullYear()+"-"+doubleNumber(dateToday.getMonth()+1)+"-"+doubleNumber(dateToday.getDate()));
            });

    </script>

@stop