@extends("operation.partial.master")




@section('content')
    @include('operation.partial.sellerQuery')
    @include('operation.partial.checkRemarksModal')

    <div id="myTabContent" class="tab-content">
        <div id="seller" class="panel panel-default tab-pane fade in active">
            <div class="sellerOverview panel-body">
                <table class="ui-table-order">
                    <thead>
                        <tr>
                            <th>买手/微信</th>
                            <th>国家/地区</th>
                            <th class="right">手机号/邮箱</th>
                            <th class="right">买手状态</th>
                            <th class="right">接单情况</th>
                            <?php $count = App\Models\Seller::count(); ?>
                            <th>买手人数：<span class="sellerNumber">{{$count}}</span></th>
                        </tr>
                    </thead>

                    <!-- 后台渲染 -->
                    @foreach($sellers as $seller)
                        <?php $url = '/operator/getSellerDetail/'. $seller->seller_id;
                        $memos = $seller->sellerMemos;
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
                            <td colspan="5"></td>
                            <td style="text-align: right;">

                                <a class="seller_memo" href="" data-toggle="modal" data-target="#checkRemarks" data-memo="{{$memo_json}}">备注</a>
                                <span class="placeholder">占位</span>
                            </td>
                        </tr>
                        <tr class="body-row">
                            <td class="info-cell clearfix" rowspan="1">
                                <img class="thumb_url"
                                     src="{{!empty($seller->user->headimgurl) ? $seller->user->headimgurl : url('/image/DefaultPicture.jpg')}}" style="margin-right: 0.5em">
                                <p style="margin-top: 1em"><span>真实姓名：</span><span class="realname">{{$seller->real_name}}</span></p>
                                <p><span>微信号：</span><span class="weixinId">{{$seller->user->wx_number}}</span></p>
                            </td>
                            <td class="country-cell" rowspan="1">
                                <p class="country">{{$seller->country->name}}</p>
                            </td>
                            <td class="email-cell" rowspan="1">
                                <?php if($seller->user->mobile!=''){
                                    $mobile = $seller->user->mobile;
                                }else{
                                    $mobile = '无';
                                } ?>
                                <p class="mobile">{{$mobile}}</p>
                                <p class="email">{{$seller->user->email}}</p>
                            </td>
                            <td class="sellerStatus-cell" rowspan="1">
                                <div class="td-cont">
                                    <p>正常</p>
                                </div>
                            </td>
                            <td class="orders-cell" rowspan="1">
                                <?php $totalNumber = $seller->seller_refuse_orders_num+$seller->seller_success_orders_num;
                                if($seller->seller_success_orders_num==0)
                                {
                                    $price = 0;
                                }
                                else{$price = $seller->seller_success_incoming/($seller->seller_success_orders_num);}
                                    ?>
                                <p><span>成功交易次数：</span><span class="ordersTimes">{{$seller->seller_success_orders_num}}</span><span> 次</span></p>
                                <p><span>成功交易均价：</span><span class="ordersAvPrice">{{sprintf('%.2f',$price)}}</span><span> 元</span></p>
                            </td>
                            <td class="edit-cell" style="text-align: right; padding-right: 2em" rowspan="1">
                                <div class="td-cont">
                                    <a class="btn btn-primary edit" href="{{url($url)}}" role="button">查看详情</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                    <!-- 后台渲染商品结束 -->
                </table>
            </div>
        </div>

    </div>
    <nav>
        {!! $sellers->render() !!}
    </nav>
    <script type="text/javascript" src={{url('js/operator/orderManagement.js')}}></script>
    <script type="text/javascript">
            $("#sellerManagement").addClass("active");
    </script>     
@stop



