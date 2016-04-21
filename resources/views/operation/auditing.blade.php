@extends("operation.partial.master")




@section('content')
    @include('operation.partial.orderQuery')
    @include('operation.partial.checkRemarksModal')

    <div id="myTabContent" class="tab-content">
        <div id="order" class="panel panel-default tab-pane fade in active">
            @include('operation.partial.orderPartial')
            <div class="detail panel-body">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th class="th1">需求/商品</th>
                        <th class="th2">订单总价/件数</th>
                        <th class="th3">国家</th>
                        <th class="th4">买手</th>
                        <th class="th5">买家联系方式</th>
                        <th class="th6">订单状态</th>
                        <th class="th7 placeholder">占个位置</th>
                    </tr>
                    </thead>
                    <!-- 后台渲染 -->
                    @foreach($suborders as $suborder)

                        <?php
                        $state = '审核中';
                        $user = $suborder->mainOrder->user;
                        $title = '';
                        $items = $suborder->items;
                        $number = 0;
                        $price_item = 0;
                        $img = [];
                        $memos = $suborder->subOrderMemos;
                        $memo_temp = [];
                        foreach ($memos as $memo) {
                            $name = App\Models\User::find($memo->hlj_id)->employee->real_name;
                            $time_memo = $memo->created_at;
                            $content = $memo->content;
                            array_push($memo_temp, array($time_memo, $content, $name));
                        }
                        $memo_json = json_encode($memo_temp);
                        if ($suborder->order_type == 0)
                        {
                            foreach ($items as $item) {
                                $title .= $item->title . ';';
                                $number += $item->detail_positive->number;
                                $price_item += ($item->price) * ($item->detail_positive->number);
                                $img = array_merge($img, $item->pic_urls);
                            }
                        }
                        else
                        {
                            foreach ($items as $item)
                            {
                                $info_trans = \App\Models\GroupItem::where('sub_order_id',$suborder->sub_order_id)->first();
                                $title .= $item->title . ';';
                                $number += $info_trans->number;
                                $price_item += ($item->price) * ($info_trans->number);
                                $img = array_merge($img, $item->pic_urls);
                            }
                        }
                        $price = $suborder->sub_order_price;
                        $title = rtrim($title, ';');
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }
                        $editUrl = 'operator/editAuditingOrder/' . $suborder->sub_order_id;
                        ?>
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                <span style="color:blue ;font-weight:bold;"><em>NO</em>:</span><span
                                        class="order_id"
                                        style="color:blue ;font-weight:bold;">{{$suborder->sub_order_number}}</span>
                                <span class="placeholder">占</span>
                                <span class="created_at">{{$suborder->updated_at}}</span>

                            </td>
                            <td>
                                <span>处理人:</span>
                                <span class="created_at">{{$suborder->operator->real_name}}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?php
                            if(count($suborder->refunds) > 0) {
                            $refunds = $suborder->refunds;
                            $title_refund = '';
                            $description = '';
                            $refund_price = '';
                            $refund_number = '';
                            $refund_time = '';
                            $refund_price_total = 0;
                            foreach ($refunds as $refund) {
                                $des = $refund->description;
                                $pos = strpos($des, "###");
                                $title_refund .= substr($des, 0, $pos) . '###';
                                $description .= substr($des, $pos + 3) . '###';
                                $refund_price .= $refund->refund_price . '###';
                                $refund_time .= $refund->created_at . '###';
                                $refund_number .= $refund->refund_inventory_count . '###';
                                $refund_price_total += $refund->refund_price;
                            }
                            $title_refund = rtrim($title_refund, "###");
                            $description = rtrim($description, "###");
                            $refund_price = rtrim($refund_price, "###");
                            $refund_number = rtrim($refund_number, "###");
                            $refund_time = rtrim($refund_time, "###");
                            ?>
                            <td style="color: red">
                                <span>已退款：</span>
                                <span>{{sprintf('%.2f',$refund_price_total)}}</span>
                                <span>元</span>
                            </td>
                            <td style="text-align: center">

                                <a class="refundRecord" href="#" data-refund-title="{{$title_refund}}"
                                   data-refund-description="{{$description}}" data-refund-time="{{$refund_time}}"
                                   data-refund-price="{{$refund_price}}"
                                   data-refund-number="{{$refund_number}}">退款记录</a>
                            <?php } else {?>
                            <td style="color: red">
                            </td>
                            <td style="text-align: center">
                                <a class="placeholder">退款记录</a>
                                <?php }?>
                                <span class="placeholder">占位</span>
                                <a class="requirement_memo"
                                   data-toggle="modal"
                                   data-target="#checkRemarks"
                                   data-memo="{{$memo_json}}"
                                   <?php if(count($memos) > 0) { ?>
                                   href="">备注 <span class="badge"
                                                    style="background-color: blueviolet">{{count($memos)}}</span></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>
                                <img class="thumb_url"
                                     src="{{isset($img[0]) ? $img[0]:'/image/DefaultPicture.jpg'}}">
                                <span class="title">{{$title}}</span>
                            </td>
                            <td class="price-cell" rowspan="1">
                                <p class="price">￥{{sprintf('%.2f',$price)}}</p>

                                <p><i>x</i><span class="total">{{$number}}</span></p>
                            </td>
                            <td class="country-cell" rowspan="1">
                                <p class="country_id">{{$suborder->country->name}}</p>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <p>{{$suborder->seller->real_name}}</p>
                            </td>
                            <td class="email-cell" rowspan="1">
                                <p class="mobile">{{$user->mobile}}</p>

                                <p class="email">{{$user->email}}</p>
                            </td>
                            <td class="itemStatus-cell" rowspan="1">
                                <div class="td-cont">
                                    <p>{{$state}}</p>

                                    <p>
                                        <?php
                                        if(Cache::get('suborder:'. $suborder->sub_order_id.':secondaryDeliver')){ ?>
                                        <span style="color:black; background-color: #35DAFA" class="label label-danger">填</span>
                                        <?php } ?>
                                        <a style="margin-left: 20px" target="_blank"
                                           href="{{$suborder->deliveryInfo->delivery_related_url}}"
                                           role="button">审查物流</a>
                                    </p>
                                </div>
                            </td>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">
                                    <a class="btn btn-primary edit" href="{{url($editUrl)}}" role="button">订单详情</a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        @endforeach
                                <!-- 后台渲染商品结束 -->

                </table>
            </div>
        </div>

        <div id="background">

        </div>

        <div id="refundRecord">
            <p class="suborder_numberOuter"><span>子订单号：</span><span class="suborder_number"></span></p>
            <hr/>
            <div class="refundDetail">
                <p><span>退款时间：</span><span class="refund_time"></span></p>

                <p><span>商品：</span><span class="refund_title"></span></p>

                <p><span>退款件数：</span><span class="refund_number"></span><span>件</span></p>

                <p><span>退款金额：</span><span class="refund_price"></span><span>元</span></p>

                <p><span>退款说明：</span><span class="refund_description"></span></p>
                <hr/>
            </div>
            <p class="justMark"><a class="btn btn-success btn-sm closeRefund" role="button">我知道了</a></p>
        </div>

        <div id="buyer" class="tab-pane fade"></div>
    </div>
    <nav>
        {!! $suborders->render()!!}
    </nav>
    <script type="text/javascript" src={{url('js/operator/orderManagement.js')}}></script>
    <script type="text/javascript">
        $("#orderAuditing").addClass("active");
        $("#orderMangement").addClass("active");
    </script>
@stop



