@extends("operation.partial.master")




@section('content')
    @include('operation.partial.requirementQuery')


    <div id="myTabContent" class="tab-content">
        <div id="order" class="panel panel-default tab-pane fade in active">
            @include('operation.partial.requirementPartial')
            <div class="detail panel-body">
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
                    @foreach($requirements as $requirement)

                        <?php
                        $requirementDetails = $requirement->requirementDetails;
                        $items = $requirement->items;
                        $number = 0;
                        $title = '';
                        $price = 0;
                        $img = [];

                        foreach ($items as $item) {
                            $title .= $item->title . '(已保存);';
                            $number += $item->skus()->first()->sku_inventory;
                            $img = array_merge($img, $item->pic_urls);
                            $price += $item->price * $item->skus()->first()->sku_inventory;
                        }
                        foreach ($requirementDetails as $detail) {
                            if (empty($detail->item_id)) {
                                $number += $detail->number;
                                $title .= $detail->title . '(未保存);';
                                $img = array_merge($img, $detail->pic_urls);
                            }
                        }
                        if (($requirement->state == 411) || ($requirement->state == 431))  {
                            if ($requirement->state == 411) {
                                $state = "买家关闭";
                            } elseif ($requirement->state == 431) {
                                $state = "运营关闭";
                            }
                            $requirementFinished = $requirement;
                            $requirementDetails = $requirementFinished->requirementDetails;
                            $number = 0;
                            $title = '';
                            $img = [];
                            $description = '';
                            if (!empty($requirementDetails)) {
                                foreach ($requirementDetails as $requirementDetail) {
                                    $number += $requirementDetail->number;
                                    $title .= $requirementDetail->title . ';';
                                    $img = array_merge($img, $requirementDetail->pic_urls);
                                    $description .= $requirementDetail->description . ';';
                                }
                            }

                        }
                        if($requirement->main_order_id != 0){
                            $mainOrder = App\Models\MainOrder::find($requirement->main_order_id);
                        }
                        if (($requirement->state == 101)&&($requirement->operator_id!=0)) {
                            $state = '待生成商品';
                            $editUrl = 'operator/generateItems/' . $requirement->requirement_id;
                        }
                        if (($requirement->state == 101)&&($requirement->operator_id==0)) {
                            $state = '需求待领取';
                            $editUrl = 'operator/editRequirement/' . $requirement->requirement_number;
                        }
                        if($requirement->state == 201) {
                            $state = '待分配订单';
                            $editUrl = 'operator/splitOrder/' . $requirement->main_order_id;
                            $postage=0;
                            if(count($mainOrder->subOrders)>0){
                                $subOrders = $mainOrder->subOrders;
                                foreach($subOrders as $subOrder)
                                {
                                    $postage += $subOrder->postage;
                                }
                                $price = $price+$postage;
                            }
                        }
                        if($requirement->state == 431) {
                            $state = '运营关闭';
                        }
                        if($requirement->state == 411) {
                            $state = '买家关闭';
                        }
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        } else {
                            $title = rtrim($title, ';');
                        }
                        ?>
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                <span style="color:blue ;font-weight:bold;"><em>NO</em>:</span> <span
                                        class="requirement_id" id="requirementNumber">{{$requirement->requirement_number}}</span>
                                <span class="placeholder">占</span>
                                <span class="created_at">{{$requirement->updated_at}}</span>
                            </td>
                            <td>
                                <?php if($requirement->operator_id != 0) { ?>
                                <span>处理人:</span>
                                <span class="created_at">{{$requirement->operator->real_name}}</span>
                                <?php } ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center">
                                <?php if(($requirement->state == 101&&$requirement->operator_id!=0)||($requirement->state == 201)) {
                                if(Auth::user()->employee->employee_id == $requirement->operator_id) {
                                ?>
                                <a class="changeOperator" style="color: yellowgreen" href="#">更换处理人</a>
                                <?php } } ?>
                                <span class="placeholder">占位</span>
                                <a class="requirement_memo" href="#">备注</a>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>
                                <img class="thumb_url"
                                     src="{{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}}">
                                <span class="title">{{rtrim($title, ";")}}</span>
                            </td>
                            <td class="price-cell" rowspan="1">

                                <?php
                                if($price != 0) { ?>
                                <p class="price">{{'￥'.sprintf('%.2f', $price)}}</p>
                                <?php
                                }
                                ?>
                                <p><i>x{{$number}}</i><span class="total"></span></p>
                            </td>
                            <td class="country-cell" rowspan="1">
                                <?php $country= '';if(($requirement->state==201)&&count($mainOrder->subOrders)>0) {
                                $subOrders = $mainOrder->subOrders;
                                foreach($subOrders as $subOrder)
                                {
                                    $country .= $subOrder->country->name .';';
                                    $seller_name = $subOrder->seller->real_name . ';';
                                }
                                $country = substr($country,0,-1);
                                $seller_name = substr($seller_name,0,-1);
                                ?>
                                <p class="country_id">{{$country}}</p>

                                <?php } else{ ?>
                                <p class="country_id">{{$requirement->country->name}}</p>
                                <?php }?>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <?php $seller_name= '';if(($requirement->state==201)&&count($mainOrder->subOrders)>0) {
                                $subOrders = $mainOrder->subOrders;
                                foreach($subOrders as $subOrder)
                                {
                                    $seller_name .= $subOrder->seller->real_name . ';';
                                }
                                $seller_name = substr($seller_name,0,-1);
                                ?>
                                <?php if(!empty($seller_name)) { ?>
                                <p>{{$seller_name}}</p>
                                <?php }} ?>
                            </td>
                            <td class="email-cell" rowspan="1">
                                <p class="mobile">{{$requirement->user->mobile}}</p>
                                <p class="email">{{$requirement->user->email}}</p>
                                <p>{{$requirement->user->nickname}}</p>
                            </td>
                            <td class="itemStatus-cell" rowspan="1">
                                <div class="td-cont">
                                    <p>{{$state}}</p>
                                    <?php if(($requirement->state == 101&&$requirement->operator_id!=0)||($requirement->state == 201)){
                                        if(Auth::user()->employee->employee_id == $requirement->operator_id) {
                                    ?>
                                    <a class="delete" href="#" role="button">关闭需求</a>
                                    <?php } } ?>
                                </div>
                            </td>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">
                                    <?php if(($requirement->state == 101)||($requirement->state == 201)){ ?>
                                    <a class="btn btn-primary edit" href="{{url($editUrl)}}" role="button">编辑</a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
        </div>

        <div id="background">
        </div>

        <!-- 更换买手页 -->

        <div id="changeOperator">
            <p><a class="btn btn-danger btn-sm closed" role="button">关闭</a></p>
            <form role="form" method="post" class="clearfix" action="{{url('operator/updateOperator')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="requirement_id" id="requirement_id">
                <label for="operator">选择买手</label>
                <div class="form-group">
                    <select class="form-control input-sm" name="operator" class="operator" id="operator">
                        <?php $employees = App\Models\Employee::get();?>
                        @foreach($employees as $employee)
                            <option value="{{$employee->employee_id}}">{{$employee->real_name}}</option>
                        @endforeach
                    </select>
                </div>
                <p style="color: red; text-align: center">更换处理人前，请充分沟通</p>
                <div style="text-align: center">
                    <button class="btn btn-default btn-sm submit">沟通过了，确认！</button>
                </div>
            </form>
        </div>
        <div id="buyer" class="tab-pane fade"></div>
    </div>
    <nav>
        {!! $requirements->appends(Input::query())->render() !!}
    </nav>
    <script type="text/javascript">
        var h=document.body.scrollHeight;
        var w=document.body.scrollWidth;
        $("#background").height(h);
        $("#background").width(w);
        window.onresize=function(){
            h=document.body.scrollHeight;
            w=document.body.scrollWidth;
            $("#background").height(h);
            $("#background").width(w);
        };
        $(document).ready(function () {
            $('#requirementAll').addClass('active');
            $("#requirementMangement").addClass("active");
            $("#order").on("click", ".delete", function (event) {
                event.preventDefault();
                var that = this;
                var deleteInfo = {
                    "requirement_id": $(that).parents("tbody").find(".requirement_id").text()
                };
                if (confirm("确认置为无效需求么？")) {
                    console.log(deleteInfo);
                    $.ajax({
                        url: "/operator/invalidRequirement/" + $(that).parents("tbody").find(".requirement_id").text(),
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

            $("#myTabContent").on("click",".changeOperator",function(event){
                event.preventDefault();
                $("#changeOperator").fadeToggle("fast");
                $("#background").fadeToggle("fast");
                var requirement_id=$(this).parents("tbody").find(".requirement_id").text();
                $("#changeOperator").find("#requirement_id").val(requirement_id);
            })

            $("#changeOperator").on("click",".closed",function(event){                   //关闭选择
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#changeOperator").fadeToggle("fast");
            })

        })
    </script>

@stop



