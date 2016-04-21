@extends("operation.partial.master")




@section('content')
    @include('operation.partial.requirementQuery')
    @include('operation.partial.checkRemarksModal')

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
                        $id = $requirement->main_order_id;
                        $mainOrder = App\Models\MainOrder::find($id);
                        $items = $mainOrder->items;
                        $number = 0;
                        $title = '';
                        $img = [];
                        $price = 0;
                        $memos = $requirement->requirementMemos;
                        $memo_temp = [];
                        foreach($memos as $memo)
                        {
                            $name = App\Models\User::find($memo->hlj_id)->employee->real_name;
                            $time_memo = $memo->created_at;
                            $content = $memo->content;
                            array_push($memo_temp,array($time_memo,$content,$name));
                        }
                        $memo_json = json_encode($memo_temp);
                        if (isset($items)) {
                            foreach ($items as $item) {
                                $number += $item->skus()->first()->sku_inventory;
                                $title .= $item->title . ';';
                                $img = array_merge($img, $item->pic_urls);
                                $price += $item->skus()->first()->sku_price * $item->skus()->first()->sku_inventory;
                            }
                        }
                        if ($requirement->state == 101) {
                            $state = '需求待处理';
                        }
                        if ($requirement->state == 201) {
                            $state = '待拆分订单 ';
                        }
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }else {
                            $title = rtrim($title,';');
                        }

                        $goOnUrl = 'operator/splitOrder/' . $requirement->main_order_id;

                        ?>

                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                <span style="color:blue ;font-weight:bold;"><em>NO</em>:</span> <span class="requirement_id" id="requirementNumber">{{$requirement->requirement_number}}</span>
                                <span class="placeholder">占</span>
                                <span class="created_at">{{$requirement->created_at}}</span>
                            </td>
                            <td>
                                <span>处理人:</span>
                                <span class="created_at">{{$requirement->operator->real_name}}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center">
                                <a class="requirement_shot placeholder" href="">查看快照</a>
                                <span class="placeholder">占位</span>
                                <a class="requirement_memo" 
                                    data-toggle="modal" 
                                    data-target="#checkRemarks" 
                                    data-memo="{{$memo_json}}"
                                    <?php if(count($memos)>0) { ?>
                                    href="">备注<span class="badge" style="background-color: blueviolet">{{count($memos)}}</span></a>
                                   <?php } ?>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>
                                <img class="thumb_url"
                                     src="{{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}}">

                                <?php
                                if($title) {
                                ?>
                                <span class="title">{{$title}}</span>
                                <?php
                                }else {
                                ?>
                                <span class="title">需求商品为空，请在订单中添加商品。</span>
                                <?php
                                }
                                ?>

                            </td>
                            <td class="price-cell" rowspan="1">
                                <?php
                                $price = $mainOrder->main_order_price;
                                if($price != 0) {
                                ?>
                                <p class="price">{{'￥'.sprintf('%.2f',$price)}}</p>
                                <?php
                                }
                                ?>


                                <p><i>x{{$number}}</i><span class="total"></span></p>
                            </td>
                            <td class="country-cell" rowspan="1">
                                <?php $country= '';if(count($mainOrder->subOrders)>0) {
                                   $subOrders = $mainOrder->subOrders;
                                   foreach($subOrders as $subOrder)
                                    {
                                        if($subOrder->is_available == 1)
                                        {
                                        $country .= $subOrder->country->name .';';
                                        }
                                    }
                                    $country = substr($country,0,-1);
                                ?>
                                    <p class="country_id">{{$country}}</p>

                                <?php } else{ ?>
                                <p class="country_id">{{$requirement->country->name}}</p>
                                <?php }?>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <?php $seller_name= '';if(count($mainOrder->subOrders)>0) {
                                    $subOrders = $mainOrder->subOrders;
                                    foreach($subOrders as $subOrder)
                                    {
                                        if($subOrder->is_available == 1)
                                        {
                                        $seller_name .= $subOrder->seller->real_name . ';';
                                        }
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
                                    <a class="delete" href="#" role="button">关闭需求</a>
                                </div>
                            </td>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">

                                    <a class="btn btn-primary edit" href="{{url($goOnUrl)}}" role="button">继续拆单</a>


                                </div>
                            </td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
        </div>

        <div id="buyer" class="tab-pane fade"></div>
    </div>
    <nav>
        {!! $requirements->render() !!}
    </nav>
    <script type="text/javascript">
        $(document).ready(function () {

            $('#requirementWaitSplitOrder').addClass('active');
            $("#requirementMangement").addClass("active");
            
            $("#order").on("click", ".delete", function (event) {
                event.preventDefault();
                var that = this;
                if (confirm("确认置为无效需求么？")) {
                    $.ajax({
                        url: "/operator/deleteRequirement/"+$(that).parents("tbody").find(".requirement_id").text(),
                        type: "get",
                        dataType: "json",
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
                                alert("后台同学跪了，没删除成功")
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



