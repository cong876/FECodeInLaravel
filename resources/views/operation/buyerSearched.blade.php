@extends("operation.partial.master")




@section('content')
    @include('operation.partial.buyerQuery')
    @include('operation.partial.checkRemarksModal')
    <div id="myTabContent" class="tab-content">
        <div id="buyer" class="panel panel-default tab-pane fade in active">
            <div class="buyerOverview panel-body">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th style="width: 25%">买家/微信</th>
                        <th class="right" style="width: 25%">手机号/邮箱</th>
                        <th class="right" style="width: 25%">购买情况</th>
                        <th class="placeholder" style="width: 25%">占个位置啊啊啊啊啊啊</th>
                    </tr>
                    </thead>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!-- 后台渲染 -->
                    @foreach($buyers as $buy_er)
                        <?php
                        $id = $buy_er->buyer_id;
                        $buyer= App\Models\Buyer::find($id);
                        $url = 'operator/buyerManagementDetail/' . $buyer->buyer_id;
                        $memos = $buyer->buyerMemos;
                        $memo_temp = [];
                        foreach($memos as $memo)
                        {
                            $name = App\Models\User::find($memo->hlj_id)->employee->real_name;
                            $time_memo = $memo->created_at;
                            $content = $memo->content;
                            array_push($memo_temp,array($time_memo,$content,$name));
                        }
                        $memo_json = json_encode($memo_temp);
                        ?>
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td colspan="3"></td>
                            <td style="text-align: right;">
                                <a class="buyer_memo" href="" data-toggle="modal" data-target="#checkRemarks" data-memo="{{$memo_json}}">备注</a>
                                <?php
                                if($buyer->user->is_subscribed == 1) {
                                ?>
                                <span class="label label-success">关注中</span>
                                <?php
                                }else {
                                ?>
                                <span class="label label-danger">已取关</span>
                                <?php
                                }
                                ?>
                                <span class="placeholder">占位</span>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="info-cell clearfix" rowspan="1">
                                <img class="thumb_url"
                                     src="{{!empty($buyer->user->headimgurl) ? $buyer->user->headimgurl : url('/image/DefaultPicture.jpg')}}" style="margin-right: 0.5em">
                                <p style="margin-top: 1em"><span>买家昵称：</span><span class="realname">{{$buyer->user->nickname}}</span></p>
                                <?php if($buyer->user->wx_number!='') { ?>
                                <p><span>微信号：</span><span class="weixinId">{{$buyer->user->wx_number}}</span></p>
                                <?php } ?>
                            </td>
                            <td class="email-cell" rowspan="1">
                                <p class="mobile">{{$buyer->user->mobile}}</p>
                                <p class="email">{{$buyer->user->email}}</p>
                            </td>
                            <td class="orders-cell" rowspan="1">
                                <p><span>购买次数：</span><span class="ordersTimes">{{$buyer->buyer_success_orders_num}}</span><span> 次</span></p>
                                <p><span>成交总额：</span><span class="ordersAvPrice">{{sprintf('%.2f',$buyer->buyer_actual_paid)}}</span><span> 元</span></p>
                            </td>
                            <td class="edit-cell" style="text-align: right; padding-right: 2em" rowspan="1">
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
        <nav>
            {!! $buyers->appends(Input::query())->render() !!}
        </nav>
        <div class="modal fade" id="checkRemarks" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width: 400px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">
                            买家备注：
                        </h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <p>巴拉巴拉巴拉巴拉。。。。。。</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript" src={{url('js/operator/orderManagement.js')}}></script>
    <script type="text/javascript">
        $("#buyerManagement").addClass("active");
    </script>
@stop



