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
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </thead>
                    <!-- 后台渲染 -->
                    @foreach($subOrders as $subOrder)

                        <?php
                        $id = $subOrder->sub_order_id;
                        $suborder = App\Models\SubOrder::find($id);
                        $user = $suborder->buyer;
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
                        switch ($suborder->sub_order_state) {
                            case '201':
                                $state = '待付款';
                                $editUrl = 'operator/editOffer/' . $suborder->sub_order_id;
                                break;
                            case '501':
                                $state = '待发货';
                                $editUrl = 'operator/editDeliveryOrder/' . $suborder->sub_order_id;
                                break;
                            case '521':
                                $state = '待审核';
                                $editUrl = 'operator/editAuditingOrder/' . $suborder->sub_order_id;
                                break;
                            case '601':
                                $state = '已发货';
                                $editUrl = 'operator/editHasDeliveredOrder/' . $suborder->sub_order_id;
                                break;
                            case '301':
                                $state = '已完成';
                                $editUrl = 'operator/editHasFinishedOrder/' . $suborder->sub_order_id;
                                break;
                            case '411':
                                $state = '买家关闭';
                                break;
                            case '431':
                                $state = '运营关闭';
                                break;
                            case '441':
                                $state = '系统关闭';
                                break;
                        }
                        if (($suborder->sub_order_state == 541) || ($suborder->sub_order_state == 241)) {
                            $state = '拒单待分配';
                            $editUrl = 'operator/editSellerAssignOrder/' . $suborder->sub_order_id;
                        }
                        foreach ($items as $item) {
                            $title .= $item->title . ';';
                            if ($item->is_positive == 1)
                            {
                                $number += $item->detail_positive->number;
                                $price_item += ($item->price) * ($item->detail_positive->number);
                            }
                            else
                            {
                                $info_trans = \App\Models\GroupItem::where('sub_order_id',$suborder->sub_order_id)->first();
                                $number += $info_trans->number;
                                $price_item += ($item->price) * ($info_trans->number);
                            }
                            $img = array_merge($img, $item->pic_urls);
                        }
                        $price = $price_item + $suborder->postage;
                        if (($suborder->sub_order_state == 411) || ($suborder->sub_order_state == 431) || ($suborder->sub_order_state == 441)) {
                            $user = $suborder->buyer;
                            $title = '';
                            $items = $suborder->items;
                            $number = 0;
                            $img = [];
                            if ($suborder->order_type == 0)
                            {
                                if (count($items) == 0)
                                {
                                    $details = $suborder->mainOrder->requirement->requirementDetails;
                                    foreach($details as $detail)
                                    {
                                        $title .= $detail->title . ';';
                                        $number += $detail->number;
                                        $img = array_merge($img,$detail->pic_urls);
                                    }
                                }
                                else
                                {
                                    foreach ($items as $item) {
                                        $title .= $item->title . ';';
                                        $number += $item->detail_positive->number;
                                        $img = array_merge($img, $item->pic_urls);
                                    }
                                }
                            }

                            else
                            {
                                foreach ($items as $item)
                                {
                                    $info_trans = \App\Models\GroupItem::where('sub_order_id',$suborder->sub_order_id)->first();
                                    $title .= $item->title . ';';
                                    $number += $info_trans->number;
                                    $img = array_merge($img, $item->pic_urls);
                                }
                            }
                            $price = $suborder->sub_order_price;
                        }
                        $title = rtrim($title, ';');
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }

                        $title_notice = mb_substr($title, 0, 12) . '...';
                        $time_offer = $suborder->created_offer_time;
                        $time_due = date('Y-m-d H:i:s', strtotime($time_offer) + 3 * 24 * 60 * 60);

                        if ((strtotime(date('Y-m-d H:i:s')) > strtotime($time_due)) && $suborder->sub_order_state == 201) {
                            $suborder->sub_order_state = 441;
                            $suborder->is_available = false;
                            $notice = new \App\Helper\WXNotice();
                            if($user->is_subscribed == 1) {
                            $notice->timeOut($user->openid, $suborder->sub_order_price, $title_notice, '未填写', $suborder->sub_order_number);
                            }
                            $suborder->save();
                        }
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
                                if ($refund->item_id == 0) {
                                    $description .= $refund->description . '###';
                                    $refund_price .= $refund->refund_price . '###';
                                    $refund_time .= $refund->created_at . '###';
                                    $refund_number .= $refund->refund_inventory_count . '###';
                                    $refund_price_total += $refund->refund_price;
                                } else {
                                    $title_refund .= substr($des, 0, $pos) . '###';
                                    $description .= substr($des, $pos + 3) . '###';
                                    $refund_price .= $refund->refund_price . '###';
                                    $refund_time .= $refund->created_at . '###';
                                    $refund_number .= $refund->refund_inventory_count . '###';
                                    $refund_price_total += $refund->refund_price;
                                }
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
                                <?php
                                if(($suborder->sub_order_state == 201) || ($suborder->sub_order_state == 241) || ($suborder->sub_order_state == 541)
                                || ($suborder->sub_order_state == 521) || ($suborder->sub_order_state == 501) || ($suborder->sub_order_state == 601)) {
                                if(Auth::user()->employee->employee_id == $suborder->operator_id) {
                                ?>
                                <a class="changeOperator" style="color: yellowgreen" href="">更换处理人</a>
                                <?php } } ?>
                                <span class="placeholder">占</span>
                                <a class="order_memo"
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
                                <p class="price"> ￥{{sprintf('%.2f',$price)}}</p>

                                <p><i>x</i><span class="total">{{$number}}</span></p>
                            </td>
                            <?php if(($suborder->sub_order_state == 541) || ($suborder->sub_order_state == 241)) { ?>
                            <td class="country-cell" rowspan="1">
                                <?php if($suborder->order_type == 0) { ?>
                                <p class="country_id">{{$suborder->mainOrder->requirement->country->name}}</p>
                                <?php } else { ?>
                                <p class="country_id">{{$suborder->country->name}}</p>
                                <?php } ?>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <p>待分配买手</p>
                            </td>
                            <?php } else { ?>
                            <td class="country-cell" rowspan="1">
                                <p class="country_id">{{$suborder->country->name}}</p>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <p>{{$suborder->seller->real_name}}</p>
                            </td>
                            <?php } ?>
                            <td class="email-cell" rowspan="1">
                                <p class="mobile">{{$user->mobile}}</p>

                                <p class="email">{{$user->email}}</p>
                            </td>
                            <td class="itemStatus-cell" rowspan="1">
                                <div class="td-cont">
                                    <p>{{$state}}</p>
                                    <?php

                                    if($suborder->sub_order_state == 601)
                                    {
                                    if (count(Cache::get('suborder:' . $suborder->sub_order_id . ':additional')) != 0) {
                                        $cache_info = unserialize(Cache::get('suborder:' . $suborder->sub_order_id . ':additional'));
                                        $delivery_url = $cache_info['delivery_related_url'];
                                    } else {
                                        $delivery_url = $suborder->deliveryInfo->delivery_related_url;
                                    }
                                     ?>
                                    <p>
                                        <?php
                                        if(Cache::get('suborder:'. $suborder->sub_order_id.':secondaryDeliver')){ ?>
                                        <span style="color:whitesmoke; background-color: #69D2E7" class="label label-danger">填</span>
                                        <?php } ?>
                                        <a class="logistics" target="_blank" href="{{$delivery_url}}"
                                           role="button">查看物流</a>
                                    </p>
                                    <?php } ?>
                                    {{--<p>--}}
                                    {{--<a class="logistics" target="_blank" href="{{$suborder->deliveryInfo->delivery_related_url}}" role="button">查看物流</a>--}}
                                    {{--</p>--}}
                                </div>
                            </td>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">
                                    <?php if(($suborder->sub_order_state != 411) && ($suborder->sub_order_state != 431) && ($suborder->sub_order_state != 441)) { ?>
                                    <a class="btn btn-primary edit" href="{{url($editUrl)}}" role="button">订单详情</a>
                                    <?php } ?>
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
        <div id="changeOperator">
            <p><a class="btn btn-danger btn-sm closed" role="button">关闭</a></p>

            <form role="form" method="post" class="clearfix" action="{{url('operator/updateOrderOperator')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="order_id" id="order_id">
                <label for="operator">选择处理人</label>

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
        {!! $subOrders->appends(Input::query())->render()!!}
    </nav>

    <script type="text/javascript" src={{url('js/operator/orderManagement.js')}}></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#orderAll").addClass("active");
            $("#orderMangement").addClass("active");
            $("#myTabContent").on("click", ".changeOperator", function (event) {
                event.preventDefault();
                $("#changeOperator").fadeToggle("fast");
                $("#background").fadeToggle("fast");
                var order_id = $(this).parents("tbody").find(".order_id").text();
                $("#changeOperator").find("#order_id").val(order_id);
            })

            $("#changeOperator").on("click", ".closed", function (event) {                   //关闭选择
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#changeOperator").fadeToggle("fast");
            })
        })
    </script>
@stop



