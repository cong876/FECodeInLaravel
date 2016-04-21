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
                        <th class="th1">订单/商品</th>
                        <th class="th2">商品总价/件数</th>
                        <th class="th3">国家</th>
                        <th class="th4">买手</th>
                        <th class="th5">买家联系方式</th>
                        <th class="th6">订单状态</th>
                        <th class="th7 placeholder">占个位置</th>
                    </tr>
                    </thead>
                    @foreach($mainOrders as $mainOrder)
                        <?php
                        if ($mainOrder->main_order_state == 201) {
                            $subOrders = $mainOrder->subOrders;
                            $country = '';
                            $seller = '';
                            $main_order = $mainOrder;
                            $title = '';
                            $img = [];
                            $number_each = '';
                            $price = '';
                            $price_each = '';
                            $state = '待发送报价 ';
                            foreach ($subOrders as $subOrder) {
                                $items = $subOrder->items;
                                $number = 0;
                                $country .= $subOrder->country->name . ';';
                                $seller .= $subOrder->seller_id . ';';
                                $price = $subOrder->sub_order_price;
                                $price_each .= '¥'. $price . ';';
                                if (isset($items)) {
                                    foreach ($items as $item) {
                                        $number += $item->skus()->first()->sku_inventory;
                                        $title .= $item->title . ';';
                                        $img = array_merge($img, $item->pic_urls);
                                        //$price += $item->skus()->first()->sku_price * $item->skus()->first()->sku_inventory;

                                    }
                                }
                                $number_each .= 'x'. $number . ';';
                                if (mb_strlen($title, 'utf-8') > 30) {
                                    $title = mb_substr($title, 0, 30) . '......';
                                }
                            }
                        }
                        $goOnUrl = 'operator/splitOrder/' . $mainOrder->main_order_id;
                        $detailUrl = 'operator/orderDetail/'.$mainOrder->main_order_id
                        ?>

                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                <span class="requirement_id" id="requirement_order_id" ><em>NO</em>:<span> 0000000000000</span>{{$main_order->main_order_id}}</span>
                                <span class="placeholder">占</span>
                                <span class="updated_at" >{{$main_order->updated_at}}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center">
                                <a  class="requirement_shot" href="{{url($detailUrl)}}">查看详情</a>
                                <span class="placeholder">占位</span>
                                <a class="requirement_memo" 
                                    data-toggle="modal" 
                                    data-target="#checkRemarks" 
                                    data-memo=""
                                    href="">备注</a>
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
                                if($price != 0) {
                                ?>
                                <p class="price">{{$price_each}}</p>
                                <?php
                                }
                                ?>
                                <p><i>{{$number_each}}</i><span class="total"></span></p>

                            </td>
                            <td class="country-cell" rowspan="1">
                                <p class="country_id">{{$country}}</p>
                            </td>
                            <td class="seller-cell" rowspan="1">
                                <p></p>
                            </td>
                            <td class="email-cell" rowspan="1">
                                <p class="mobile">{{$main_order->user->mobile}}</p>
                                <p class="email">{{$main_order->user->email}}</p>
                                <p>{{$requirement->user->nickname}}</p>
                            </td>
                            <td class="itemStatus-cell" rowspan="1">
                                <div class="td-cont">
                                    <p>{{$state}}</p>
                                </div>
                            </td>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">
                                    <a class="btn btn-primary edit" href="{{url($goOnUrl)}}" role="button">发送报价</a>
                                </div>
                            </td>
                        </tr>
                        </tbody>

                    @endforeach
                </table>
            </div>
        </div>
        <div id="seller" class="tab-pane fade"></div>
    </div>
    <nav>
        {!! $mainOrders->render() !!}
    </nav>
    <script>
        $('#requirementWaitSendPrice').addClass('active');
        $("#requirementMangement").addClass("active");
    </script>

@stop



