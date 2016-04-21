@extends("operation.partial.master")


@section('content')
    @include('operation.partial.remittanceQuery')
    @include('operation.partial.checkRemarksModal')

    <div id="myTabContent" class="tab-content">
        <div id="remittanceHeader" class="panel panel-default tab-pane fade in active">
            @include('operation.partial.remittancePartial')
            <div class="detail panel-body">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th class="th1">订单/商品</th>
                        <th class="th2">国家/买手</th>
                        <th class="th3">买家联系方式</th>
                        <th class="th4">订单状态</th>
                        <th class="th5">处理人/打款人</th>
                        <th class="th6">实付/打款金额</th>
                        <th class="th7 placeholder">占个位置</th>
                    </tr>
                    </thead>
                    <!-- 后台渲染 -->
                    @foreach($subOrders as $suborder)
                        <?php
                        $items = $suborder->items;
                        $title = '';
                        $pic_url = [];
                        $memos = $suborder->subOrderMemos;
                        $memo_temp = [];
                        foreach($memos as $memo)
                        {
                            $name = App\Models\User::find($memo->hlj_id)->employee->real_name;
                            $time_memo = $memo->created_at;
                            $content = $memo->content;
                            array_push($memo_temp,array($time_memo,$content,$name));
                        }
                        $memo_json = json_encode($memo_temp);
                        foreach($items as $item)
                        {
                            $title .= $item->title.';';
                            $pic_url = array_merge($pic_url,$item->pic_urls);
                        }
                        if($suborder->sub_order_state == 601)
                        {
                            $url = 'operator/editHasDeliveredOrder/'.$suborder->sub_order_id;
                            $state = '已发货';
                        }
                        elseif($suborder->sub_order_state == 301)
                        {
                            $url = 'operator/editHasFinishedOrder/'.$suborder->sub_order_id;
                            $state = '已完成';
                        }
                        $title = rtrim($title,';');

                        ?>
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td  colspan="2" style="text-align: left;padding-left: 1em;">
                                <span style="color:blue ;font-weight:bold;"><em>NO</em>:</span>
                                <span class="order_id" style="color:blue ;font-weight:bold;">{{$suborder->sub_order_number}}</span>
                                <span class="placeholder">占</span>
                                <span class="created_at">{{$suborder->updated_at}}</span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?php
                            if(count($suborder->refunds)>0) {
                            $refunds = $suborder->refunds;
                            $title_refund = '';
                            $description = '';
                            $refund_price = '';
                            $refund_number = '';
                            $refund_time = '';
                            $refund_price_total=0;
                            foreach($refunds as $refund) {
                                $des = $refund->description;
                                $pos = strpos($des,"###");
                                $title_refund .= substr($des,0,$pos).'###';
                                $description .= substr($des,$pos+3).'###';
                                $refund_price .= $refund->refund_price.'###';
                                $refund_time .= $refund->created_at.'###';
                                $refund_number .= $refund->refund_inventory_count.'###';
                                $refund_price_total += $refund->refund_price;
                            }
                            $title_refund = rtrim($title_refund,"###");
                            $description = rtrim($description,"###");
                            $refund_price = rtrim($refund_price,"###");
                            $refund_number = rtrim($refund_number,"###");
                            $refund_time = rtrim($refund_time,"###");
                            ?>

                            <td style="text-align: center">
                                <a class="refundRecord" href="#" data-refund-title="{{$title_refund}}" data-refund-description="{{$description}}" data-refund-time="{{$refund_time}}" data-refund-price="{{$refund_price}}" data-refund-number="{{$refund_number}}">退款记录</a>
                                <span class="placeholder">占</span>
                                <a class="order_memo" 
                                    data-toggle="modal" 
                                    data-target="#checkRemarks" 
                                    data-memo="{{$memo_json}}"
                                    <?php if(count($memos)>0) { ?>
                                    href="">备注 <span class="badge" style="background-color: blueviolet">{{count($memos)}}</span></a>
                                    <?php } ?>
                            </td>
                            <?php } else{ ?>
                            <td style="text-align: center">
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
                            <?php } ?>
                        </tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>
                                <img class="thumb_url"
                                     src="{{isset($pic_url[0])? $pic_url[0] :url('/image/DefaultPicture.jpg')}}">
                                <span class="title">{{$title}}</span>
                            </td>
                            <td class="seller-cell" rowspan="1">
                                <p class="sellerCountry">{{$suborder->country->name}}</p>
                                <p class="seller">{{$suborder->seller->real_name}}</p>
                            </td>
                            <td class="buyer-cell" rowspan="1">
                                <p class="buyerMoible">{{$suborder->mainOrder->user->mobile}}</p>
                                <p class="buyerEmail">{{$suborder->mainOrder->user->email}}</p>
                            </td>
                            <td class="order-status-cell" rowspan="1">
                                <p>{{$state}}</p>
                            </td>
                            <td class="operator-cell" rowspan="1">
                                <p>{{$suborder->operator->real_name}}</p>
                                <p class="remittance-operator"><span>/</span><span class="operator">李文娟</span></p>
                            </td>
                            <td class="remittance-cell" rowspan="1">
                                <p>￥<span class="order_price">{{sprintf("%.2f",$suborder->sub_order_price-$suborder->refund_price)}}</span></p>
                                <p class="orderFunds"><span>/</span><span class="remittanced">{{sprintf("%.2f",$suborder->transfer_price)}}</span></p>
                            </td>
                            <?php
                            if($suborder->transferReason!='')
                                {
                                    $reason = $suborder->transferReason->reason;
                                }
                            else {$reason = '';}
                                $payment_json = json_encode($suborder->seller->user->paymentmethods);
                                $hlj_id = $suborder->seller->hlj_id;
                                $payment = App\Models\PaymentMethod::where('hlj_id',$hlj_id)->
                                where('is_default',true)->first();
                                if(!$payment)
                                {
                                    $payment_id = '';
                                }
                                else
                                {
                                    $payment_id = $payment->payment_methods_id;
                                }
                            ?>
                            <td class="edit-cell" style="text-align: center" rowspan="1">
                                <div class="td-cont">
                                    {{--当登陆人为文娟时显示按钮--}}
                                    <?php if(Auth::user()->hlj_id==6){ ?>
                                    <a
                                        class="btn btn-xs btn-success remittance"
                                        style="margin-bottom: 0.5em"
                                        href=""
                                        role="button"
                                        data-toggle="modal"
                                        data-target="#sureToRemittance"
                                        data-payment="{{$payment_json}}"
                                        data-reason="{{$reason}}"
                                        data-payment_methods_id="{{$payment_id}}"
                                            >
                                        确认打款
                                    </a><br />
                                     <?php } ?>
                                    <a
                                        class="btn btn-xs btn-primary"
                                        href="{{url($url)}}"
                                        role="button">
                                        订单详情
                                    </a>
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
            {!! $subOrders->render() !!}
        </nav>

        <div class="modal fade" id="sureToRemittance" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width: 400px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                          &times;
                        </button>
                    </div>
                    <div class="modal-body remittanceInfo" style="max-height: 500px; overflow-y: auto">
                        <strong>打款账号</strong>
                        <hr />
                        <p class="paymentInfo">
                            <span class="channel"></span>
                            <span class="placeholder">占位</span>
                            <span class="isDefault" style="color: red"></span><br />
                            <span class="account_name"></span>
                            <span class="placeholder">占位</span>
                            <span class="identification"></span>
                        </p>
                        <p class="placeholder split">占</p>
                        <p class="paymentInfo">
                            <span class="channel"></span>
                            <span class="placeholder">占位</span>
                            <span class="isDefault" style="color: red"></span><br />
                            <span class="account_name"></span>
                            <span class="placeholder">占位</span>
                            <span class="identification"></span>
                        </p>
                        <hr />
                        <strong>提现方式</strong>
                        <hr />
                        <div class="input-group">
                            <select class="form-control" id="paymentSelect" disabled="disabled">
                                <option value="">银行卡</option>
                                <option value="">支付宝</option>
                            </select>
                            <button type="button" class="btn btn-default changePayment">更改</button>
                        </div>
                        <hr />
                        <strong>打款金额</strong>
                        <hr />
                        <p>
                            <span>打款金额：</span>
                            <span class="remittanced"></span>
                            <span>RMB</span>
                        </p>
                        <hr />
                        <strong>原因</strong>
                        <hr />
                        <p class="reason"></p>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button type="button" class="btn btn-success completed" data-order-id>已经打款</button>
                    </div>
                </div>
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
    </div>
    <script type="text/javascript" src={{url('js/operator/orderManagement.js')}}></script>
    <script type="text/javascript">
        $(document).ready(function(){
        
            $("#fundsManagement").addClass("active");
            $("#waitRemittance").addClass("active");
            $("#remittanceManagement").addClass("active");
        
            $("#remittanceHeader").on("click",".remittance",function(event){
                event.preventDefault();
                $("#sureToRemittance").find(".channel").text("");
                $("#sureToRemittance").find(".isDefault").text("");
                $("#sureToRemittance").find(".account_name").text("");
                $("#sureToRemittance").find(".identification").text("");
                $("#paymentSelect").find("option").eq(0).attr({"value":""});
                $("#paymentSelect").find("option").eq(1).attr({"value":""});
                var orderIndex=$(this).parents("tbody");
                $("#sureToRemittance").find(".completed").data("order-id",orderIndex.find(".order_id").text());
                $("#sureToRemittance").find(".remittanced").text(orderIndex.find(".remittanced").text());
                var payment=$(this).data("payment");
                var paymentInfo=$("#sureToRemittance").find(".paymentInfo");
                $("#paymentSelect").find("option").removeAttr("selected");
                for(var i=0; i<payment.length; i++){
                    paymentInfo.eq(i).find(".account_name").text(payment[i].account_name);
                    paymentInfo.eq(i).find(".identification").text(payment[i].identification);
                    if(payment[i].payment_methods_id==$(this).data("payment_methods_id")){
                        paymentInfo.eq(i).find(".isDefault").text("默认");
                        if(payment[i].channel==1){
                            $("#paymentSelect").find("option").eq(0).attr({"selected":"selected"});
                        }else{
                            $("#paymentSelect").find("option").eq(1).attr({"selected":"selected"});
                        }
                    }else{
                        paymentInfo.eq(i).find(".isDefault").text("")
                    };
                    if(payment[i].channel==1){
                        paymentInfo.eq(i).find(".channel").text("银行卡");
                        $("#paymentSelect").find("option").eq(0).attr({"value":payment[i].payment_methods_id});
                    }else{
                        paymentInfo.eq(i).find(".channel").text("支付宝");
                        $("#paymentSelect").find("option").eq(1).attr({"value":payment[i].payment_methods_id});
                    }
                };
                if(payment.length==1){
                    paymentInfo.eq(1).hide();
                    $("#sureToRemittance").find(".split").hide();
                }else{
                    paymentInfo.eq(1).show();
                    $("#sureToRemittance").find(".split").show();
                };
                if(payment.length==0){
                   $("#sureToRemittance").find(".channel").text("该买手没有添加提现方式，请提醒他添加。"); 
                }
                $("#sureToRemittance").find(".reason").text($(this).data("reason"))
            })

            $("#sureToRemittance").on("click",".changePayment",function(event){
                event.preventDefault();
                if($(this).text()=="更改"){
                    $("#paymentSelect").removeAttr("disabled");
                    $(this).text("保存");
                }else{
                    $("#paymentSelect").attr({"disabled":"disabled"});
                    $(this).text("更改");
                }
            })

            $("#sureToRemittance").on("click",".completed",function(event){
                event.preventDefault();
                if($("#paymentSelect").attr("disabled")=="disabled"){
                    if($("#paymentSelect").val()!=""){
                        window.location.href="/operator/hasTransfer/"+$(this).data("order-id")+"/"+$("#paymentSelect").val();
                    }else{
                        alert("该买手没有添加提现方式，请提醒他添加。");
                    }
                }else{
                    alert("请先保存提现方式");
                }
            })

        })
    </script>
@stop



