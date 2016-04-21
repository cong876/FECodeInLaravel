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
                        $state = '待付款';
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
                                $info_trans = App\Models\GroupItem::where('sub_order_id',$suborder->sub_order_id)->first();
                                $title .= $item->title . ';';
                                $number += $info_trans->number;
                                $price_item += ($item->price) * ($info_trans->number);
                                $img = array_merge($img, $item->pic_urls);
                            }
                        }
                        $price = $price_item + $suborder->postage;
                        $title = rtrim($title, ';');
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }
                        $editUrl = 'operator/editOffer/' . $suborder->sub_order_id;
                        $time_offer = $suborder->created_offer_time;
                        ?>
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                <span style="color:blue ;font-weight:bold;"><em>NO</em>:</span><span
                                        class="order_id"
                                        style="color:blue ;font-weight:bold;">{{$suborder->sub_order_number}}</span>
                                <span class="placeholder">占</span>
                                <span class="created_at">{{$time_offer}}</span>

                            </td>
                            <td>
                                <span>处理人:</span>
                                <span class="created_at">{{$suborder->operator->real_name}}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center">
                                <?php $memoUrl = 'operator/addOrderMemo/'. $suborder->sub_order_id?>
                                <a class="addMemo"
                                   data-toggle="modal"
                                   data-target="#addRemarks"
                                   data-url="{{url($memoUrl)}}"
                                   href="">添加备注</a>
                                <span class="placeholder">占位</span>
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
                                        <a class="delete" href="#" role="button">关闭交易</a>
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
                <div class="modal fade" id="addRemarks" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" style="width: 400px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                </button>
                                <h4 class="modal-title">
                                    添加备注：
                                </h4>
                            </div>
                            <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                                <form role="form" method="post" class="clearfix" action="">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <div class="form-group">
                    <textarea
                            class="form-control input-sm"
                            name="order_memo"
                            class="changeRefundsReason"
                            placeholder="在这里添加订单备注"></textarea>
                                    </div>
                                    <div style="text-align: center">
                                        <button type="submit" class="btn btn-default">提交</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                </table>
            </div>
        </div>

        <div id="buyer" class="tab-pane fade"></div>
    </div>

    <nav>
        {!! $suborders->render()!!}
    </nav>
    <script type="text/javascript">
        $(document).ready(function () {

            $("#orderWaitPay").addClass("active");
            $("#orderMangement").addClass("active");

            $("#order").on("click", ".delete", function (event) {
                event.preventDefault();
                var that = this;
                if (confirm("关闭交易是高危操作，请务必和买家充分沟通过再进行此操作！")) {
                    window.location.href = "/operator/cancelOrder/" + $(that).parents("tbody").find(".order_id").text();
                }
            })
            $("#order").on("click",".addMemo",function (event) {
                event.preventDefault();
                var that = this;
                $("#addRemarks").find("form").attr({"action":$(that).data("url")});
            })

        })
    </script>
@stop



