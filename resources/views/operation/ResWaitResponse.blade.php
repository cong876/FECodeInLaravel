@extends("operation.partial.master")




@section('content')
    @include('operation.partial.requirmentQuery')
    <div id="myTabContent" class="tab-content">
        <div id="order" class="panel panel-default tab-pane fade in active">
            <div class="detailHeader panel-body">
                <ul class="nav nav-tabs">
                    <li>
                        <a href="#">全部</a>
                    </li>
                    <li class="active">
                        <a href="#">待报价</a>
                    </li>
                    <li>
                        <a href="#">待付款</a>
                    </li>
                    <li>
                        <a href="#">待发货</a>
                    </li>
                    <li>
                        <a href="#">已发货</a>
                    </li>
                    <li>
                        <a href="{{url('operator/showFinishedRequirement')}}">已完成</a>
                    </li>
                    <li>
                        <a href="#">已删除</a>
                    </li>
                </ul>
            </div>
            <div class="detailHerder2">
                <ul class="nav nav-pills">
                    <li class="disabled"><a class="placeholder">占个位置</a></li>
                    <li class="active"><a href="{{url('operator/waitResponse')}}">待生成商品</a></li>
                    <li><a href="{{url('operator/waitSplit')}}">待分配订单</a></li>
                    <li><a href="{{url('operator/waitSendPrice')}}">待发送报价</a></li>
                </ul>
            </div>
            <div class="detail panel-body" id="demo">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th class="th1">需求/商品</th>
                        <th class="th2">商品总价/件数</th>
                        <th class="th3">国家</th>
                        <th class="th4">买手</th>
                        <th class="th5">买家联系方式</th>
                        <th class="th6">需求状态</th>
                        <th class="th7 placeholder">占个位置</th>
                    </tr>
                    </thead>
                        <tbody v-repeat="req: requirements">
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                需求号:<span class="requirement_id" v-text="req.requirement_id"></span>
                                <span class="placeholder">占</span>
                                <span class="created_at" v-text="req.created_at"></span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center">
                                <a class="requirement_shot" href="#">查看快照</a>
                                <span class="placeholder">占位</span>
                                <a class="requirement_memo" href="#">备注</a>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>
                                <img class="thumb_url"
                                     src="@{{req.requirement_details[0].pic_urls[0]}}">
                                <span class="title" v-text="req.requirement_details[0].title"></span>
                            </td>
                            <td class="price-cell" rowspan="1">


                                <p class="price"></p>


                                <p><i>x@{{req.requirement_details[0].number}}</i><span class="total"></span></p>
                            </td>
                            <td class="country-cell" rowspan="1">
                                <p class="country_id" v-text="req.country.name"></p>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <p></p>
                            </td>
                            <td class="email-cell" rowspan="1">
                                <p class="mobile" v-text="req.user.mobile"></p>

                                <p class="email" v-text="req.user.email"></p>
                            </td>
                            <td class="itemStatus-cell" rowspan="1">
                                <div class="td-cont">
                                    <p></p>

                                    <p>
                                        <a class="delete" href="#" role="button">无效需求</a>
                                    </p>
                                </div>
                            </td>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">
                                    <a class="btn btn-primary edit" href="" role="button">编辑</a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>
        </div>
        <div id="buyer" class="tab-pane fade"></div>
    </div>

    <nav>
        <ul class="pagination" id="pageIt">
            <li v-repeat="last_page">
                <a v-on='click: page($event, ($index+1))' href="#"><span>@{{$index+1}}</span></a>
            </li>
        </ul>
    </nav>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#demo',
            data: {
                requirements: null
            }
        });

        var page = new Vue({
            el: '#pageIt',
            data: {
                last_page: 0
            },
            methods: {
                page: function(e, index){
                    console.log(index);
                    e.preventDefault();
                    $.ajax({
                        url: "/getRes/?page="+index,
                        type: "get",
                        dataType: "json",
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {
                            vm.requirements = response.data;
                            current = index;
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    });
                }
            }
        });
        $(document).ready(function () {
            $.ajax({
                url: "getRes",
                type: "get",
                dataType: "json",
                beforeSend: function (xhr) {
                    var token = $("meta[name=csrf-token]").attr('content');
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    }
                },
                success: function (response) {
                    vm.requirements = response.data;
                    page.last_page = response.last_page;
                },
                error: function (request, errorType, errorMessage) {
                    alert("error:" + errorType + ";  message:" + errorMessage);
                }
            });
            $("#order").on("click", ".delete", function (event) {
                event.preventDefault();
                var that = this;
                var deleteInfo = {
                    "requirement_id": $(that).parents("tbody").find(".requirement_id").text()
                };
                if (confirm("确认置为无效需求么？")) {
                    console.log(deleteInfo);
                    $.ajax({
                        url: "/operator/invalidRequirement",
                        type: "get",
                        dataType: "json",
                        data: deleteInfo,            //要删除的商品或子需求的信息
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {
                            if (response == 1) {
                                alert("删除成功");
                                $(that).parents("tbody").remove();
                            } else {
                                alert("没删除成功，请联系开发同学！")
                            }
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    })
                }
            })
        })
    </script>

@stop



