@extends("operation.partial.master")




@section('content')
    <div id="myTabContent" class="tab-content">
        <div id="staff" class="panel panel-default tab-pane fade in active">
            <div class="buyerOverview panel-body">
                <table class="ui-table-order">
                    <thead>
                        <tr>
                            <th>头像</th>
                            <th>姓名</th>
                            <th>微信号</th>
                            <th>手机号</th>
                            <th>邮箱</th>
                            <th>编辑</th>
                        </tr>
                    </thead>
                    @foreach($employees as $employee)
                    <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td colspan="5"></td>
                            <td style="text-align: right;">
                                <span class="placeholder">占位</span>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="info-cell clearfix" rowspan="1">
                                <img style="width: 60px; height: 60px;" src="{{$employee->user->headimgurl}}">
                            </td>
                            <td>
                                <p>{{$employee->real_name}}</p>
                            </td>
                            <td>
                                <p>{{$employee->user->wx_number}}</p>
                            </td>
                            <td>
                                <p>{{$employee->user->mobile}}</p>
                            </td>
                            <td>
                                <p>{{$employee->user->email}}</p>
                            </td>
                            <?php $url = 'employee/getEmployeeDetail/' . $employee->employee_id ?>
                            <td>
                                <div class="td-cont">
                                    <a class="btn btn-primary edit" href="{{url($url)}}" role="button">查看详情</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <!-- 后台渲染商品结束 -->
                    @endforeach
                </table>
            </div>
        </div>

    </div>
    <script type="text/javascript" src={{url('js/operator/orderManagement.js')}}></script>
    <script type="text/javascript">
            $("#staffManagement").addClass("active");
    </script>
@stop



