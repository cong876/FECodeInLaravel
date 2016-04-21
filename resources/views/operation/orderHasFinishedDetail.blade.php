@extends("operation.partial.master")

@section('content')
    <?php

    $regionInstance = \App\Helper\ChinaRegionsHelper::getInstance();

    ?>
    <div id="orderDetail">
        <div class="detail panel-body">
            <table class="ui-table-order">
                <thead>
                <tr>
                    <th class="thItem">商品</th>
                    <th class="thPrice">商品单价(元)</th>
                    <th class="thNumber">数量</th>
                    <th class="thDescription">备注</th>
                    <th class="thStatus placeholder">商品状态</th>
                    <th class="thCreatItem placeholder">生成商品</th>
                </tr>
                <tr class="header-row">
                    <td style="text-align: left">
                        <span class="placeholder">占</span>
                        订单号:<span class="requirement_id">{{$suborder->sub_order_number}}</span>
                        <span class="placeholder">占</span>
                        <span class="created_at">{{$suborder->updated_at}}</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php
                    $items = $suborder->items;
                    $memos = DB::table('sub_order_memos')->where('sub_order_id', $suborder->sub_order_id)->where('is_available', 1)->orderBy('created_at', 'desc')->get();
                    $number = 0;
                    ?>
                    <td style="text-align: center">
                        <?php if(count($memos) > 0) { ?>
                        <a class="order_memo"
                           data-toggle="modal"
                           data-target="#checkRemarks"
                           href="">备注 <span class="badge" style="background-color: blueviolet">{{count($memos)}}</span></a>
                        <span class="placeholder">占</span>
                        <?php } ?>
                        <a class="addMemo"
                           data-toggle="modal"
                           data-target="#addRemarks"
                           data-url=""
                           href="">添加备注</a>
                    </td>
                </tr>
                </thead>
                @foreach($items as $item)
                    <?php
                    if ($item->is_positive == true) {
                        $number += $item->detail_positive->number;
                        $number_limited = $item->detail_positive->number;
                        $op_memos = $item->detail_positive->hlj_admin_response_information;
                        $buyer_memos = $item->detail_positive->hlj_buyer_description;
                    } else {
                        $info_trans = \App\Models\GroupItem::where('sub_order_id', $suborder->sub_order_id)->first();
                        $number += $info_trans->number;
                        $op_memos = "";
                        $buyer_memos = $info_trans->memos;
                        $number_limited = $info_trans->number;
                    }
                    $refund_records = App\Models\OrderRefund::where('item_id', $item->item_id)->get();
                    if (count($refund_records) == 0) {
                        $refund_count = 0;
                    } else {
                        $refund_count = 0;
                        foreach ($refund_records as $refund_record) {
                            $refund_count += $refund_record->refund_inventory_count;
                        }
                    }
                    $img = $item->pic_urls;
                    $img_json = json_encode($img);
                    $inventory = $number_limited - $refund_count;
                    $title = $item->title;
                    if (mb_strlen($title, 'utf-8') > 30) {
                        $title = mb_substr($title, 0, 30) . '......';
                    }
                    ?>
                    <tbody class="requirement" data-item-id="{{$item->item_id}}" data-inventory="{{$inventory}}"
                           data-limited-number="{{$number_limited}}" data-item-title="{{$item->title}}">
                    <tr class="separation-row"></tr>
                    <tr class="body-row">
                        <td class="title-cell clearfix" rowspan="1">
                            <span class="placeholder">占位</span>
                            <img class="thumb_url" src="{{isset($img[0])? $img[0]:'/image/DefaultPicture.jpg'}}">
                            <span data-img="{{$img_json}}" class="imageContainer"></span>
                            <span data-opnote-id="{{isset($op_memos)? $op_memos:""}}" class="operatingNotes"></span>
                            <span class="title">{{$title}}</span>
                        </td>
                        <td class="price-cell" rowspan="1">
                            <p>
                                <span>￥</span>
                                <span class="price">{{$item->price}}
                                </span>
                            </p>
                        </td>
                        <td class="number-cell">
                            <p><i>x</i><span class="number">{{$number_limited}}</span></p>
                        </td>
                        <td class="description-cell">
                            <p class="description">{{isset($buyer_memos)? $buyer_memos:""}}</p>
                        </td>
                        <td class="status-cell">
                            <?php $refunds = $suborder->refunds;
                            $count = 0;
                            foreach ($refunds as $refund) {
                                if ($refund->item_id == $item->item_id) {
                                    $count += 1;
                                }
                            } if($count >= 1){ ?>
                            <span style="color:red">已退款</span>
                            <?php } ?>
                        </td>
                        <td class="change-cell">
                            <p>
                                <a class="btn btn-primary btn-sm checkOut" role="button">查看详情</a>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                @endforeach
                <caption class="buyerInfo">
                    <p>
                        <span class="widthFixed">国家:<span>{{$suborder->country->name}}</span></span>
                        <span>邮费:<span>{{$suborder->postage}}</span></span>
                        <span class="showRight"><span>{{$number}}</span>件商品</span>
                    </p>

                    <p>
                        <span class="widthFixed">买手:<span>{{$suborder->seller->real_name}}</span></span>
                        <span class="showRight">总价:<span
                                    class="totalPrice">{{$suborder->sub_order_price}}</span>元</span>
                    </p>

                    <p class="innerRight">
                        <span style="display: inline-block; float: left; color: blue">已完成</span>
                        <?php if(count($suborder->refunds) > 0) {?>
                        <a class="refundRecord" href="#">退款记录</a>
                        <?php } ?>
                    </p>
                </caption>
            </table>
            <div class="alert alert-info" role="alert" style="margin-top: 10px">
                <p>
                    <strong>买家信息</strong>
                </p>

                <p>
                    <span>手机号码：</span>
                    <span class="mobile">{{$suborder->mainOrder->user->mobile}}</span></br/>
                    <span>邮箱：</span>
                    <span class="email">{{$suborder->mainOrder->user->email}}</span>
                </p>
                <hr/>
                <p>
                    <strong>收货地址</strong>
                </p>

                <p>
                    <?php
                    $province_code = $suborder->receivingAddress->first_class_area;
                    $city_code = $suborder->receivingAddress->second_class_area;
                    $county_code = $suborder->receivingAddress->third_class_area;
                    $street_address = $suborder->receivingAddress->street_address;
                    $province_level = $regionInstance->getRegionByCode($province_code)->name;
                    if ($city_code == 1) {
                        $city_level = "";
                    } else {
                        $city_level = $regionInstance->getRegionByCode($city_code)->name;
                    }
                    if ($county_code == 1) {
                        $county_level = "";
                    } else {
                        $county_level = $regionInstance->getRegionByCode($county_code)->name;
                    }
                    ?>
                    <span>收件人：</span>
                    <span class="receiver">{{$suborder->receivingAddress->receiver_name}}</span><br/>
                    <span>手机号码：</span>
                    <span class="receiver_mobile">{{$suborder->receivingAddress->receiver_mobile}}</span><br/>
                    <span>地址：</span>
                    <span class="receiver_address">{{$province_level}}{{$city_level}}{{$county_level}}{{$street_address}}</span><br/>
                    <span>邮编：</span>
                    <span class="receiver_zip_code">{{$suborder->receivingAddress->receiver_zip_code}}</span>
                </p>
                <?php if($suborder->delivery_info_id != 0){?>
                <hr/>
                <p>
                    <strong>物流信息</strong>
                </p>

                <p>
                    <?php
                    if(count(Cache::get('suborder:' . $suborder->sub_order_id . ':additional')) != 0) {
                    $cache_info = unserialize(Cache::get('suborder:' . $suborder->sub_order_id . ':additional'));
                    if($cache_info['delivery_company_id'] != 0) {
                    $company_id = $cache_info['delivery_company_id'];
                    $delivery_company_name = App\Models\DeliveryCompany::find($company_id)->company_name;
                    ?>
                    <span>物流公司：</span><span>{{$delivery_company_name}}</span><br/>
                    <span>物流单号：</span><span>{{$cache_info['delivery_order_number']}}</span>
                    <?php } else{?>
                    <span>物流公司：</span><span>{{$cache_info['delivery_company_info']}}</span><br/>
                    <span>物流单号：</span><span>{{$cache_info['delivery_order_number']}}</span>
                    <?php } ?>
                    <?php } else { if(($suborder->deliveryInfo->delivery_company_id) != 0) { ?>
                    <span>物流公司：</span><span>{{$suborder->deliveryInfo->deliveryCompany->company_name}}</span><br/>
                    <span>物流单号：</span><span>{{$suborder->deliveryInfo->delivery_order_number}}</span>
                    <?php } else{?>
                    <span>物流公司：</span><span>{{$suborder->deliveryInfo->delivery_company_info}}</span><br/>
                    <span>物流单号：</span><span>{{$suborder->deliveryInfo->delivery_order_number}}</span>
                    <?php } } ?>
                </p>
                <?php }?>
                <hr/>
                <p>
                    <span>处理人：</span>
                    <span class="">{{$suborder->operator->real_name}}</span>
                </p>
                <hr/>
                <p>
                    <span>报价时间：</span>
                    <span class="offerTime">{{$suborder->created_offer_time}}</span>
                    <br/>
                    <span>付款时间：</span>
                    <span class="offerTime">{{$suborder->payment_time}}</span>
                    <br/>
                    <span>发货时间：</span>
                    <span class="sendTime">{{$suborder->delivery_time}}</span>
                    <br/>
                    <span>完成时间：</span>
                    <span class="sendTime">{{$suborder->updated_at}}</span>
                </p>
            </div>
        </div>

        <!-- 查看退款记录页 -->
        <div id="refundRecord">
            <p><span>子订单号：</span><span class="suborder_number">{{$suborder->sub_order_number}}</span></p>
            <hr/>
            <?php if(count($suborder->refunds) > 0) { $refunds = $suborder->refunds; ?>
            @foreach($refunds as $refund)
                <?php if($refund->refund_type == 1){ $des = $refund->description; $pos = strpos($des, "###"); $title_refund = substr($des, 0, $pos); $description = substr($des, $pos + 3); ?>
                <p><span>退款时间：</span><span class="refundTime">{{$refund->created_at}}</span></p>
                <p><span>商品：</span><span class="item_title">{{$title_refund}}</span></p>
                <p><span>退款件数：</span><span class="item_number">{{$refund->refund_inventory_count}}</span><span>件</span>
                </p>
                <p><span>退款金额：</span><span class="refund">{{$refund->refund_price}}</span><span>元</span></p>
                <p><span>退款说明：</span><span>{{$description}}</span></p>
                <hr/>
                <?php } elseif($refund->refund_type == 2) { ?>
                <p><span>退全款</span></p>
                <p><span>退款金额：</span><span class="refund">{{$refund->refund_price}}</span><span>元</span></p>
                <p><span>退款说明：</span><span>{{$refund->description}}</span></p>
                <?php } ?>
            @endforeach
            <?php } else {?>
            <p><span>空</span></p>
            <?php } ?>
            <p><a class="btn btn-success btn-sm closeRefund" role="button">我知道了</a></p>
        </div>

        <!-- 更换买手页 -->
        <div id="changeBuyer">
            <?php $countries = App\Models\Country::get();
            $url = '/operator/updateSeller/' . $suborder->sub_order_id;
            $memoUrl = 'operator/addOrderMemo/' . $suborder->sub_order_id;
            ?>
            <p><a class="btn btn-danger btn-sm closed" role="button">关闭</a></p>

            <form role="form" method="post" class="clearfix" action="{{$url}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <label for="buyer_country">选择国家</label>

                <div class="form-group">
                    <select class="form-control input-sm" name="buyer_country" class="buyer_country" id="buyer_country">
                        <option value="0">--请选择--</option>
                        @foreach($countries as $country)
                            <option value="{{$country->country_id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
                <label for="buyer">选择买手</label>

                <div class="form-group">
                    <select class="form-control input-sm" name="buyer" class="buyer" id="buyer">
                        <option value="0">--请选择--</option>
                    </select>
                </div>
                <p style="color: red; text-align: center">更换买手前，请先与新旧两位买手充分沟通</p>

                <div style="text-align: center">
                    <button class="btn btn-default btn-sm submit">沟通过了，确认！</button>
                </div>
            </form>

        </div>

        <!-- 商品详情页 -->
        <div id="editRequireDetail">
            <a class="btn btn-danger closed" role="button">关闭</a>

            <form role="form" method="post" action="">

                <label for="itemTitle">商品名称</label>

                <div class="form-group">
                    <textarea name="itemTitle" class="itemTitle form-control" rows="2" readonly="readyonly"></textarea>
                </div>

                <label for="price">商品单价</label>

                <div class="input-group" style="padding:0 50% 0 0">
                    <input type="text" name="price" class="form-control price" readonly="readyonly">
                    <span class="input-group-addon">RMB</span>
                </div>

                <label for="number">商品数量</label>

                <div class="input-group" style="padding:0 50% 0 0">
                    <input type="text" name="number" class="form-control number" readonly="readyonly">
                    <span class="input-group-addon">件</span>
                </div>

                <label for="description">备注</label>

                <div class="form-group">
                    <textarea name="description" class="description form-control" rows="3"
                              readonly="readyonly"></textarea>
                </div>

                <label for="operatingNotes">运营备注</label>

                <div class="form-group">
                    <textarea name="operatingNotes" class="operatingNotes form-control" rows="3"
                              readonly="readyonly"></textarea>
                </div>

                <div class="form-group imageArea">
                    <div class="showImageEx">
                        <img src="" class="pic_urls">
                    </div>
                    <div class="addImage placeholder">
                    </div>
                    <div class="clearfix"></div>
                </div>

            </form>
        </div>

        <div class="modal fade" id="checkRemarks" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width: 400px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">
                            订单备注：
                        </h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <ul>
                            @foreach($memos as $memo)
                                <?php $hlj_id = $memo->hlj_id;
                                $name = App\Models\User::find($hlj_id)->employee->real_name; ?>
                                <li class="remarks" data-id="{{$memo->sub_order_memo_id}}">
                                    <p>
                                        <span>备注时间：</span>
                                        <span>{{$memo->created_at}}</span>
                                        <span>  备注人：</span>
                                        <span>{{$name}}</span>
                                        <span class="placeholder">占位</span>
                                        <?php if((Auth::user()->hlj_id == $memo->hlj_id) || (Auth::user()->employee->op_level > 3)) { ?>
                                        <button type="button" class="close delete">&times;</button>
                                        <?php } ?>
                                    </p>
                                    <p>{{$memo->content}}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

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
                        <form role="form" method="post" class="clearfix" action="{{url($memoUrl)}}">
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

        <!-- 背景 -->
        <div id="background">

        </div>

        <!-- 图片轮播页 -->
        <div id="myCarousel" class="carousel slide" data-interval="false">
            <!-- 轮播（Carousel）指标 -->
            <ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0"></li>
            </ol>
            <!-- 轮播（Carousel）项目 -->
            <div class="carousel-inner">
                <div class="item">
                    <img src="" alt="First slide">
                </div>
            </div>
            <!-- 轮播（Carousel）导航 -->
            <a class="carousel-control left" style="line-height: 600px; font-size: 40px; background-image: none"
               href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-circle-arrow-left"
                                                          style="color: white;"></span></a>
            <a class="carousel-control right" style="line-height: 600px; font-size: 40px; background-image: none"
               href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-circle-arrow-right"
                                                          style="color: white"></span></a>
        </div>

    </div>




    <script type="text/javascript">

        $(document).ready(function () {

            $("#orderMangement").addClass("active");
            var h = document.body.scrollHeight;
            var w = document.body.scrollWidth;
            if (h < $("#editRequireDetail").height()) {
                h = $("#editRequireDetail").height() + 60;
            }
            ;
            $("#background").height(h);
            $("#background").width(w);
            window.onresize = function () {
                h = document.body.scrollHeight;
                w = document.body.scrollWidth;
                if (h < $("#editRequireDetail").height()) {
                    h = $("#editRequireDetail").height() + 60;
                }
                ;
                $("#background").height(h);
                $("#background").width(w);
            };

            var editIndex = 0;

            $("#orderDetail").on("click", ".refundRecord", function (event) {                      //查看退货记录页
                event.preventDefault();
                var that = this;
                $("#refundRecord").find(".suborder_number").text($(that).parents("tbody").find(".order_id").text());
                $("#refundRecord").fadeToggle("fast");
                $("#background").fadeToggle("fasts");
            })

            $("#refundRecord").on("click", ".closeRefund", function (event) {
                event.preventDefault();
                $("#refundRecord").fadeToggle("fast");
                $("#background").fadeToggle("fasts");
            })

            $("#orderDetail").on("click", ".checkOut", function (event) {                  //查看商品详情
                event.preventDefault();
                editIndex = $(this).parents("tbody").prevAll("tbody").length;
                var requirementIndex = $(this).parents(".requirement");
                $("#editRequireDetail").find(".itemTitle").val(requirementIndex.data("item-title"));
                $("#editRequireDetail").find(".price").val($.trim(requirementIndex.find(".price").text()));
                $("#editRequireDetail").find(".number").val(requirementIndex.find(".number").text());
                $("#editRequireDetail").find(".description").val(requirementIndex.find(".description").text());
                $("#editRequireDetail").find(".operatingNotes").val(requirementIndex.find(".operatingNotes").data("opnote-id"))
                var imgIndex = requirementIndex.find(".imageContainer").data("img");
                if (!requirementIndex.find(".imageContainer").attr("data-img") == "") {
                    for (var i = 0; i < imgIndex.length; i++) {
                        var showImage = $("#editRequireDetail").find(".showImageEx").clone().attr({"class": "showImage"});
                        showImage.children("img").attr({"src": imgIndex[i]});
                        $("#editRequireDetail").find(".addImage").before(showImage);
                    }
                    ;
                } else {
                    var showImage = $("#editRequireDetail").find(".showImageEx").clone().attr({"class": "showImage"});
                    showImage.children("img").attr({"src": $(this).parents("tbody").find(".thumb_url").attr("src")});
                    $("#editRequireDetail").find(".addImage").before(showImage);
                }
                $("#background").fadeToggle("fast");
                $("#editRequireDetail").fadeToggle("fast");
            })

            $("#editRequireDetail").on("click", "img", function (event) {         //显示图片轮播
                event.preventDefault();
                event.stopImmediatePropagation();
                var that = this;
                var imageIndex = $(that).parents(".imageArea").children(".showImage");
                var imageNumber = imageIndex.length;
                for (var i = 0; i < imageNumber; i++) {
                    if (i == 0) {
                        $("#myCarousel").find(".item").children("img").attr({"src": imageIndex.eq(0).children("img").attr("src")})
                    } else {
                        var olIndex = $("#myCarousel").children(".carousel-indicators");
                        var addLi = olIndex.children("li").first().clone();
                        addLi.attr({"data-slide-to": i});
                        olIndex.append(addLi);
                        var divIndex = $("#myCarousel").children(".carousel-inner");
                        var addDiv = divIndex.children("div").first().clone();
                        addDiv.children("img").attr({"src": imageIndex.eq(i).children("img").attr("src")});
                        divIndex.append(addDiv);
                    }
                }
                $("#myCarousel").find("li").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").find(".item").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").show();
            })

            $("#background").on("click", function (event) {                       //关闭图片轮播
                event.preventDefault();
                var len = $("#myCarousel").find("li").length;
                $("#myCarousel").hide();
                setTimeout(function () {
                    for (var i = 0; i < len; i++) {
                        if (i == 0) {
                            $("#myCarousel").find("li").eq(0).attr({"class": ""});
                            $("#myCarousel").find(".item").eq(0).attr({"class": "item"});
                        } else {
                            $("#myCarousel").find("li").eq(1).remove();
                            $("#myCarousel").find(".item").eq(1).remove();
                        }
                    }
                }, 500)
            })

            $("#editRequireDetail").on("click", ".closed", function (event) {               //关闭编辑
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#editRequireDetail").fadeToggle("fast");
                var imageNumber = $("#editRequireDetail").find(".showImage").length;
                for (var i = 0; i < imageNumber; i++) {
                    $("#editRequireDetail").find(".showImage")[0].remove();
                }
                ;
                var input = $("#editRequireDetail").find("input");
                var textarea = $("#editRequireDetail").find("textarea");
                for (var i = 0; i < input.length; i++) {
                    input.eq(i).val("")
                }
                ;
                for (var i = 0; i < textarea.length; i++) {
                    textarea.eq(i).val("")
                }
            })

            $("#orderDetail").on("click", ".deleteOrder", function (event) {                 //关闭交易
                event.preventDefault();
                event.stopImmediatePropagation();
                if (confirm("关闭交易是高危操作，请三思！三思完，请温柔地告知买家关闭的理由：)")) {
                    window.location.href = "/operator/cancelOrder/" + $(".requirement_id").text();
                }
            })


        })
    </script>

@stop

