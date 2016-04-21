@extends("operation.partial.master")




@section('content')
    <?php
    $user = $mainOrder->user;
    // 所有主订单原始item_ids
    DB::setFetchMode(PDO::FETCH_ASSOC);
    $main_item_ids = DB::table('item_main_order')->where('main_order_id',
            $mainOrder->main_order_id)->select('item_id')->get();
    $main_item_ids = array_flatten(array_values($main_item_ids));
    $sub_item_ids = [];
    $remain_item_ids = [];
    // 所有子订单item_ids
    $suborders = $mainOrder->subOrders;
    foreach ($suborders as $suborder) {
        $sub_order_temp = DB::table('item_sub_order')->where('sub_order_id',
                $suborder->sub_order_id)->select('item_id')->get();
        if (!$sub_item_ids) {
            $sub_item_ids = array_flatten(array_values($sub_order_temp));
        } else {
            $sub_item_ids = array_merge($sub_item_ids, array_flatten(array_values($sub_order_temp)));
        }

    }
    $remain_item_ids = array_diff($main_item_ids, $sub_item_ids);

    $requirement = $mainOrder->requirement;
    $requirement_id = $requirement->requirement_id;
    $operator_id = $requirement->operator_id;
    $requirement_number = $requirement->requirement_number;
    $requirement_created_at = $requirement->created_at;
    $memos = $requirement->requirementMemos;
    $url = 'operator/addRequirementMemo/'. $requirement->requirement_id;
    ?>
    <div id="divideOrder">
        <div class="detailHeader panel-body" ondrop="drop2(event)" ondragover="allowDrop(event)">
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
                        需求号:<span class="requirement_id">{{$requirement_number}}</span>
                        <span class="mainOrder" data-mainorder="{{$mainOrder->main_order_id}}"></span> <!-- 主订单id -->
                        <span class="placeholder">占</span>
                        <span class="created_at">{{$requirement_created_at}}</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: center">
                        <a class="requirement_memo" 
                            data-toggle="modal" 
                            data-target="#checkRemarks"
                            href="">备注</a>
                        <span class="placeholder">占</span>                    
                        <a class="addMemo" 
                            data-toggle="modal" 
                            data-target="#addRemarks" 
                            data-url="{{url($url)}}"
                            href="">添加备注</a>                        
                    </td>
                </tr>
                </thead>
                
                <tbody class="requirement" style="display: none" draggable="true" ondragstart="drag(event)" data-suborder="" data-item-title="">
                    <tr class="separation-row"></tr>
                    <tr class="body-row">
                        <td class="title-cell clearfix" rowspan="1">
                            <span class="placeholder">占位</span>
                            <img class="thumb_url" src="">
                            <span data-img="" class="imageContainer"></span>
                            <span class="item_id" item_id=""></span>
                            <span class="title"></span>
                        </td>
                        <td class="price-cell" rowspan="1">
                            <p class="price">0</p>
                        </td>
                        <td class="number-cell">
                            <p><i>x</i><span class="number">0</span></p>
                        </td>
                        <td class="description-cell">
                            <p class="description"
                               data-opnote=""></p>
                        </td>
                        <td class="status-cell placeholder">
                            <p class="status"></p>
                        </td>
                        <td class="change-cell">
                            <p>
                                <a class="btn btn-primary change" role="button">更改</a>
                            </p>

                            <p>
                                <a class="btn btn-danger deleteThis" role="button">删除</a>
                            </p>
                        </td>
                    </tr>
                </tbody>
                
                <!-- 后台渲染部分 -->
                {{--渲染未分配订单--}}
                @foreach($remain_item_ids as $remain_item_id)
                    <?php
                    $imgStr = '';
                    $item = \App\Models\Item::find($remain_item_id);
                    $images = $item->pic_urls;
                    if (!empty($images)) {
                        $imgStr = implode(',', $images);
                    }
                    ?>
                    <tbody class="requirement" draggable="true" ondragstart="drag(event)" data-suborder="" data-item-title="{{$item->title}}">
                    <tr class="separation-row"></tr>
                    <tr class="body-row">
                        <td class="title-cell clearfix" rowspan="1">
                            <span class="placeholder">占位</span>
                            <img class="thumb_url" src="{{isset($images[0]) ? $images[0]: '/image/DefaultPicture.jpg'}}">
                            <span data-img="{{isset($imgStr) ? $imgStr : ''}}" class="imageContainer"></span>
                            <span class="item_id" item_id="{{$item->item_id}}"></span>
                            <span class="title">{{$item->title}}</span>
                        </td>
                        <td class="price-cell" rowspan="1">
                            <p class="price">{{$item->skus()->first()->sku_price}}</p>
                        </td>
                        <td class="number-cell">
                            <p><i>x</i><span class="number">{{$item->skus()->first()->sku_inventory}}</span></p>
                        </td>
                        <td class="description-cell">
                            <p class="description"
                               data-opnote="{{$item->detail_positive->hlj_admin_response_information}}">{{$item->detail_positive->hlj_buyer_description
                            }}</p>
                        </td>
                        <td class="status-cell placeholder">
                            <p class="status"></p>
                        </td>
                        <td class="change-cell">
                            <p>
                                <a class="btn btn-primary change" role="button">更改</a>
                            </p>

                            <p>
                                <a class="btn btn-danger deleteThis" role="button">删除</a>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                    @endforeach
                            <!-- 后台渲染结束 -->
                    <caption>
                        <a class="btn-sm btn-success addItem" role="button">增加商品</a>
                        <a class="btn-sm btn-danger deleteMainOrder" role="button">删除主订单</a>

                        <p style="float:right">当前需求总价:￥<span class="totalPrice">{{sprintf("%.2f", isset($mainOrder->main_order_price) ? $mainOrder->main_order_price : 0)}}</span>
                        </p>
                    </caption>
            </table>
        </div>

        {{--渲染已经拆分的订单--}}

        @foreach($suborders as $suborder)
            <?php
            if($suborder->sub_order_state != 431) {
            $country_id = $suborder->country_id;
            $items = $suborder->items;
            $seller_id = $suborder->seller_id;
            ?>
            <div class="detail panel-body" data-suborder="{{$suborder->sub_order_id}}">
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
                    </thead>
                    @foreach($items as $item)
                        <?php
                        $images = $item->pic_urls;
                        if (!empty($images)) {
                            $imgStr = implode(',', $images);
                        }
                        ?>
                        <tbody class="requirement" draggable="false" data-suborder="{{$suborder->sub_order_id}}" data-item-title="{{$item->title}}">
                        <tr class="separation-row"></tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>

                                <img class="thumb_url"
                                     src="{{isset($item->pic_urls[0]) ? $item->pic_urls[0]: '/image/DefaultPicture.jpg'}}">
                                <span data-img="{{isset($imgStr) ? $imgStr : ''}}" class="imageContainer"></span>
                                <span class="item_id" item_id="{{$item->item_id}}"></span>
                                <span class="title">{{$item->title}}</span>
                            </td>
                            <td class="price-cell" rowspan="1">
                                <p class="price">{{$item->skus()->first()->sku_price}}</p>
                            </td>
                            <td class="number-cell">
                                <p><i>x</i><span class="number">{{$item->skus()->first()->sku_inventory}}</span></p>
                            </td>
                            <td class="description-cell">
                                <p class="description"
                                   data-opnote="{{$item->detail_positive->hlj_admin_response_information}}">{{$item->detail_positive->hlj_buyer_description
                            }}</p>
                            </td>
                            <td class="status-cell placeholder">
                                <p class="status"></p>
                            </td>
                            <td class="change-cell">
                                <p>
                                    <a class="btn btn-primary change" href="#" role="button"
                                       style="display: none">更改</a>
                                </p>

                                <p>
                                    <a class="btn btn-danger deleteThis" href="#" role="button"
                                       style="display: none">删除</a>
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    @endforeach
                    <caption style="position: relative">
                        <select class="country" disabled="disabled" style="width: 100px; height: 2em">
                            <option>--请选择--</option>
                            <?php $countries = DB::table('countries')->get() ?>
                            @foreach($countries as $country)
                                @if($country['country_id'] == $country_id)
                                    <option value="{{$country['country_id']}}" selected>{{$country['name']}}</option>
                                @else
                                    <option value="{{$country['country_id']}}">{{$country['name']}}</option>
                                @endif
                            @endforeach
                        </select>
                        <select class="buyer" disabled="disabled" style="width: 100px; height: 2em">
                            <option value="0">--请选择--</option>
                            <?php
                            $sellers = App\Models\Seller::where('country_id',$country_id)->where('is_available','true')->get();
                            $seller_show = App\Models\Seller::where('seller_id',$seller_id)->first();
                            ?>
                            <option value="{{$seller_show->seller_id}}" selected>{{$seller_show->real_name}}</option>
                                   @foreach($sellers as $seller)
                                   @if($seller != $seller_show)
                                    <option value="{{$seller->seller_id}}">{{$seller->real_name}}</option>
                                    @endif
                                  @endforeach
                        </select>
                        <label for="postage">邮费</label>
                        <input class="postage" type="number" style="width: 100px" readonly="readonly" value="{{$suborder->postage}}">
                        <a class="btn-sm btn-success saveOrder" role="button" style="display: none">保存</a>
                        <a class="btn-sm btn-primary editOrder" role="button" style="display: inline">编辑</a>
                        <a class="btn-sm btn-danger deleteChildOrder" role="button" style="display: inline">删除订单</a>

                        <p style="float:right">子订单总价:￥<span class="totalPrice">{{
                        sprintf('%.2f',$suborder->sub_order_price)}}</span></p>
                    </caption>
                </table>
            </div>
                <?php  } ?>
        @endforeach
   
        <div class="detailEx panel-body" style="display:none" ondrop="drop2(event)" ondragover="allowDrop(event)" data-suborder="">
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
                </thead>

                <caption>
                    <select class="country" style="width: 100px; height: 2em">
                        <option>--请选择--</option>    
                        <?php $countries = DB::table('countries')->get() ?>
                        @foreach($countries as $country)
                            <option value="{{$country['country_id']}}">{{$country['name']}}</option>
                        @endforeach
                    </select>
                    <select class="buyer" style="width: 100px; height: 2em">
                        <option value="0">--请选择--</option>
                    </select>
                    <label for="postage">邮费</label>
                    <input class="postage" type="number" style="width: 100px">
                    <a class="btn-sm btn-success saveOrder" role="button">保存</a>
                    <a class="btn-sm btn-primary editOrder" role="button">编辑</a>
                    <a class="btn-sm btn-danger deleteChildOrder" role="button">删除订单</a>
                    <p style="float:right">子订单总价:￥<span class="totalPrice"></span></p>
                </caption>
            </table>
        </div>
        <div class="alert alert-info detailFooter" role="alert">
            <strong>买家信息</strong><br/>
            <span>手机号码：</span>
            <span class="mobile">{{$user->mobile}}</span>
            <span>邮箱：</span>
            <span class="email">{{$user->email}}</span>
        </div>

        <div class="panel-body">
            <p class="buttonarea">
                <a class="btn btn-default sendIndexOrder" role="button" style="width: 120px;">发送报价</a>
            </p>
        </div>

        <div id="editRequireDetail">
            <a class="btn btn-danger closed" role="button">关闭</a>
            <a class="btn btn-primary save" role="button">保存</a>
            <span data-state="" class="state"></span>

            <form role="form">
                <label for="itemTitle">商品名称</label>

                <div class="form-group">
                    <textarea name="itemTitle" class="itemTitle form-control" rows="2"></textarea>
                </div>
                <label for="price">商品单价</label>

                <div class="input-group" style="padding:0 50% 0 0">
                    <input type="number" name="price" class="form-control price">
                    <span class="input-group-addon">RMB</span>
                </div>
                <label for="number">商品数量</label>

                <div class="input-group" style="padding:0 50% 0 0">
                    <input type="number" name="number" class="form-control number">
                    <span class="input-group-addon">件</span>
                </div>
                <label for="description">备注</label>

                <div class="form-group">
                    <textarea name="description" class="description form-control" readonly="readonly"
                              rows="3"></textarea>
                </div>
                <label for="opnote">运营备注</label>

                <div class="form-group">
                    <textarea name="opnote" class="opnote form-control" rows="3"></textarea>
                </div>
                <div class="form-group imageArea">
                    <div class="showImageEx">
                        <img src="" class="pic_urls">
                        <a class="deleteImage" href="">×</a>
                    </div>
                    <div class="addImage">
                        <input type="file" class="chosefiles">
                        <span class="addicon">+</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group savearea">

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
                            需求备注：
                        </h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <ul>
                            @foreach($memos as $memo)
                                <?php $hlj_id = $memo->hlj_id;
                                $name = App\Models\User::find($hlj_id)->employee->real_name
                                ?>
                                <li class="remarks" data-id="{{$memo->requirement_memo_id}}">
                                    <p>
                                        <span>备注时间：</span>
                                        <span>{{$memo->created_at}}</span>
                                        <span>  备注人：</span>
                                        <span>{{$name}}</span>
                                        <span class="placeholder">占位</span>
                                        <?php if((Auth::user()->hlj_id == $memo->hlj_id)||(Auth::user()->employee->op_level>3)) { ?>
                                        <button type="button" class="close delete">&times;</button>
                                        <?php  } ?>
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
                        <form role="form" method="post" class="clearfix" action="">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <textarea 
                                    class="form-control input-sm"
                                    name="requirement_memo"
                                    class="changeRefundsReason"
                                    placeholder="在这里添加需求备注"></textarea>
                            </div>
                            <div style="text-align: center"><button type="submit" class="btn btn-default">提交</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="background">

        </div>

        <div id="myCarousel" class="carousel slide">
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

        $("#requirementMangement").addClass("active");
        function allowDrop(ev) {               //允许元素拖动
            ev.preventDefault();
        }

        var blank;

        function drag(ev) {                     //拖动时的操作函数，拖动时获取被拖动元素ID以及获取原订单
            ev.dataTransfer.setData("Text", ev.target.id);
            blankHeader = $("#" + ev.dataTransfer.getData("Text")).parents(".detailHeader"); //从主订单拖出来
            blank = $("#" + ev.dataTransfer.getData("Text")).parents(".detail");            //从子订单拖出来
            // $("#"+ev.dataTransfer.getData("Text")).data("from",blank.data("suborder"))
        }

        document.ondragend = function (ev) {       //拖动结束时的操作函数，判断原订单是否为空，以及重新计算总价
            ev.preventDefault();
            if (blank.find("tbody").length == 0 && blank.data("suborder") == "") {
                blank.remove();
            };
            calculate(blank);
            // calculate(blankHeader);
        }

        function drop1(ev) {                   //直接拖动到订单外所进行的操作：新建一个子订单
            ev.preventDefault();
            var data = ev.dataTransfer.getData("Text");
            if (data[0] == "t") {
                var newDetail = $("#divideOrder").children(".detailEx").eq(0).clone();
                newDetail.attr({"class": "detail panel-body", "style": "display:block"});
                $("#divideOrder").children(".detailFooter").eq(0).before(newDetail);
                var tbodyLen = newDetail.find("tbody").length;
                for (var i = 0; i < tbodyLen; i++) {
                    newDetail.find("tbody").eq(0).remove();
                }
                newDetail.find("caption").before($("#" + data));
                calculate(newDetail)
            }
            $(newDetail).find('.country').eq(2).hide();
            $(newDetail).find('.buyer').eq(2).hide();
        }
        function drop2(ev) {                //直接拖动到已有子订单所进行的操作
            ev.preventDefault();
            ev.stopImmediatePropagation();
            var data = ev.dataTransfer.getData("Text");
            if ($(ev.target).hasClass("detail")) {
                $(ev.target).find("caption").before($("#" + data));
                calculate($(ev.target))
            } else {
                $(ev.target).parents(".detail").find("caption").before($("#" + data));
                calculate($(ev.target).parents(".detail"))
            }
        }

        function calculate(that) {             //计算订单内总价的函数
            var len = that.find(".price").length;
            var totalPrice = 0;
            for (var i = 0; i < len; i++) {
                totalPrice = totalPrice + parseFloat(that.find(".price").eq(i).text()) * parseFloat(that.find(".number").eq(i).text());
            }
            ;
            if (!((that.find(".postage").eq(0).val() == "")||(that.find(".postage").eq(0).val() == undefined))) {
                totalPrice = totalPrice + parseFloat(that.find(".postage").eq(0).val())
            };
            if (that.hasClass("detailHeader")) {
                for (var i = 0; i < $(".detail").length; i++) {
                    // console.log(totalPrice);
                    // debugger;
                    // console.log(parseFloat($(".detail").eq(i).find(".totalPrice").text()));
                    totalPrice = totalPrice + parseFloat($(".detail").eq(i).find(".totalPrice").text())
                }
            }
            that.find(".totalPrice").text(totalPrice.toFixed(2));
        }


        $(document).ready(function () {
            var h = document.body.scrollHeight;
            var w = document.body.scrollWidth;
            if (h < $("#editRequireDetail").height()) {
                h = $("#editRequireDetail").height() + 60;
            };
            $("#background").height(h);
            $("#background").width(w);
            $("#myCarousel").height($("#editRequireDetail").height());

            window.onresize = function () {
                h = document.body.scrollHeight;
                w = document.body.scrollWidth;
                if (h < $("#editRequireDetail").height()) {
                    h = $("#editRequireDetail").height() + 60;
                };
                $("#background").height(h);
                $("#background").width(w);
                $("#myCarousel").height($("#editRequireDetail").height());
            }

            var editIndex = 0;

            $("body").attr({"ondrop": "drop1(event)", "ondragover": "allowDrop(event)"});

            for (var i = 0; i < $("tbody").length; i++) {                               //为每件商品添加一个id
                $("tbody").eq(i).attr({"id": "tb" + i})
            }

            $("#divideOrder").on("click", ".addItem", function (event) {              //增加商品
                event.preventDefault();
                editIndex = 0;
                $("#background").slideToggle("fast");
                $("#editRequireDetail").slideToggle("fast");
            })

            $("#divideOrder").on("click", ".deleteMainOrder", function (event) {      //删除主订单
                event.preventDefault();
                if (confirm("删除主订单后，本页所有子订单都将被删除，确认删除么？")) {
                    $.ajax({
                        url: "/operator/deleteMainOrder/" + $(".mainOrder").data("mainorder"),
                        type: "get",
                        dataType: "json",
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {
                            if(response.status==414){
                                alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                                return false;
                            };
                            if (response) {
                                alert("删除成功！");
                                window.location.href = response;
                            } else {
                                alert("删除失败，请联系开发同学！")
                            }
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    })
                }
            })
            
            $("#divideOrder").on("input",".postage",function(event){             //填写完邮费后更改子订单和主订单总价
                event.preventDefault();
                event.stopImmediatePropagation;
                calculate($(this).parents(".detail"));
                calculate($(".detailHeader").eq(0))
            })

            // document.getElementsByClassName

            $("#divideOrder").on("click", ".saveOrder", function (event) {             //保存子订单
                event.preventDefault();
                var that = this;
                if($(this).parent("caption").find(".buyer").val()==0){
                    alert("请分配买手");
                    return false
                };
                if($(this).parent("caption").find(".postage").val()==""){
                    alert("请填写邮费后再保存");
                    return false
                };
                calculate($(that).parents(".detail"));
                var tbody = $(that).parents(".detail").find("tbody");
                var mainOrder_id = $(".mainOrder").data("mainorder");
                var subOrder_id = $(that).parents(".detail").data("suborder");
                var postage = $(that).parents(".detail").find(".postage").val();
                var subOrderPrice = $(that).parents(".detail").find(".totalPrice").text();
                var childOrder = {
                    "mainOrder_id": mainOrder_id,
                    "to": subOrder_id,
                    "postage": postage,
                    "subOrderPrice": subOrderPrice,
                    "item": [],
                    "country_id": $(that).parents(".detail").find(".country").val(),
                    "seller_id":$(that).parents(".detail").find(".buyer").val()
                };
                for (var i = 0; i < tbody.length; i++) {
                    var item = {
                        "item_id": "",
                        "subOrder_number": ""
                    }
                    item.item_id = tbody.eq(i).find(".item_id").attr("item_id");
                    item.subOrder_number = tbody.eq(i).data("suborder")
                    if (!(item.subOrder_number == subOrder_id)) {
                        childOrder.item.push(item);
                    } else if (item.subOrder_number == "") {
                        childOrder.item.push(item);
                    }
                };
                console.log(childOrder);
                $.ajax({
                    url: "/operator/dealSubOrder",
                    type: "post",
                    dataType: "json",
                    data: childOrder,
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response) {
                        if(response.status==414){
                            alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                            return false;
                        };
                        $(that).parents(".detail").data("suborder", response.subOrder_id);
                        for (var i = 0; i < tbody.length; i++) {
                            tbody.eq(i).data("suborder", response.subOrder_id)
                        }
                        $(that).hide();
                        $(that).next("a").show();
                        $(that).next("a").next("a").show();
                        $(that).prev("input").attr({"readonly": "readyonly"});
                        $(that).parents(".detail").find("select.country").attr({"disabled": "disabled"});
                        $(that).parents(".detail").find("select.buyer").attr({"disabled": "disabled"});
                        var childOrder = $(that).parents(".detail");
                        childOrder.removeAttr("ondragover");
                        childOrder.removeAttr("ondrop");
                        for (var i = 0; i < childOrder.find("tbody").length; i++) {
                            childOrder.find("tbody").eq(i).attr({"draggable": "false"});
                            childOrder.find("tbody").eq(i).removeAttr("ondragstart");
                            childOrder.find(".change").eq(i).hide();
                            childOrder.find(".deleteThis").eq(i).hide()
                        };
                        calculate(childOrder);
                        calculate($(".detailHeader").eq(0))
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                })
            })

            $("#divideOrder").on("click", ".editOrder", function (event) {            //编辑子订单
                event.preventDefault();
                $(this).hide();
                $(this).next("a").hide();
                $(this).prev("a").show();
                $(this).prev("a").prev("input").removeAttr("readonly");
                $(this).parents(".detail").find("select.country").removeAttr("disabled");
                $(this).parents(".detail").find("select.buyer").removeAttr("disabled");
                var childOrder = $(this).parents(".detail");
                childOrder.attr({"ondragover": "allowDrop(event)"});
                childOrder.attr({"ondrop": "drop2(event)"});
                for (var i = 0; i < childOrder.find("tbody").length; i++) {
                    childOrder.find("tbody").eq(i).attr({"draggable": "true"})
                    childOrder.find("tbody").eq(i).attr({"ondragstart": "drag(event)"});
                    childOrder.find(".change").eq(i).show();
                    childOrder.find(".deleteThis").eq(i).show();
                }
            })

            $("#divideOrder").on("change",".country",function(event){
                event.preventDefault();
                event.stopImmediatePropagation();
                var that=this;
                $.ajax({
                    url:"/operator/getBuyer/"+$(that).val(),
                    type:"get",
                    dataType:"json",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        };
                    },
                    success: function (response) {
                        if(response.status==414){
                            alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                            return false;
                        };
                        $(that).next("select").html("<option value='0'>--请选择--</option>");
                        for(var i=0; i<response[0].length; i++){
                            var newSeller=$("<option></option>");
                            newSeller.attr({"value":response[0][i],"data-pingyin":response[2][i],"data-abbreviation":response[3][i]});
                            newSeller.text(response[1][i]);
                            $(that).next("select").append(newSeller);
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                })               
            })

            $("#divideOrder").on("click", ".deleteChildOrder", function (event) {        //删除子订单
                event.preventDefault();
                var that = this;
                if (confirm("确认删除？")) {
                    var subOrder_id = $(that).parents(".detail").data("suborder");
                    if (subOrder_id == undefined) {
                        subOrder_id = "";
                    };
                    var url = "/operator/deleteSubOrder/" + subOrder_id
                    console.log(subOrder_id);
                    $.ajax({
                        url: url,
                        type: "get",
                        dataType: "json",
                        //data: deleteInfo,            //要删除的商品或子需求的信息
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {
                            if(response.status==414){
                                alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                                return false;
                            };
                            if (response == 1) {
                                $(that).parents(".detail").remove();
                                alert("删除成功！");
                                calculate($(".detailHeader").eq(0));
                            } else {
                                alert("删除失败，请联系开发同学！")
                            }
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    })
                }
            })

            $("#divideOrder").on("click", ".change", function (event) {                  //编辑商品详情
                event.preventDefault();
                editIndex = $(this).parents("tbody").attr("id");
                var requirementIndex = $(this).parents(".requirement");
                $("#editRequireDetail").find(".itemTitle").val(requirementIndex.data("item-title"));
                $("#editRequireDetail").find(".price").val(parseFloat(requirementIndex.find(".price").text()).toFixed(2));
                $("#editRequireDetail").find(".number").val(requirementIndex.find(".number").text());
                $("#editRequireDetail").find(".description").val(requirementIndex.find(".description").text());
                $("#editRequireDetail").find(".opnote").val(requirementIndex.find(".description").data("opnote"));
                var imgIndex = requirementIndex.find(".imageContainer").attr("data-img").split(",");
                if (!requirementIndex.find(".imageContainer").attr("data-img") == "") {
                    for (var i = 0; i < imgIndex.length; i++) {
                        var showImage = $("#editRequireDetail").find(".showImageEx").clone().attr({"class": "showImage"});
                        showImage.children("img").attr({"src": imgIndex[i]});
                        $("#editRequireDetail").find(".addImage").before(showImage);
                    }
                };
                $("#background").slideToggle("fast");
                $("#editRequireDetail").slideToggle("fast");
            })

            $("#editRequireDetail").on("change",".price",function(event){
                event.preventDefault();
                $(this).val($(this).val()>0.01 ? parseFloat($(this).val()).toFixed(2) : 0.01);
            })

            $("#editRequireDetail").on("change",".number",function(){
                event.preventDefault();
                $(this).val($(this).val()>1 ? parseInt($(this).val()) : 1);
            })

            $("#divideOrder").on("change",".postage",function(event){
                event.preventDefault();
                $(this).val($(this).val()>0 ? parseFloat($(this).val()).toFixed(2) : 0);
            })

            $("#editRequireDetail").on("change", ".chosefiles", function () {                 //上传图片
                var that = this;
                var fileUploadControl = $(this)[0];
                if ($(that).parent("div").parent("div").children("div").length > 11) {
                    alert("最多上传9张图片");
                    return false;
                };
                if (fileUploadControl.files.length > 0) {
                    var file = fileUploadControl.files[0];
                    var name = fileUploadControl.files[0]['name'];
                    var avFile = new AV.File(name, file);
                    avFile.save().then(function (json) {
                        var newurl = json._url;     //原图片地址
                        var thumb = avFile.thumbnailURL(210, 210);    //压缩图片地址
                        var showImage = $("#editRequireDetail").find(".showImageEx").clone().attr({"class": "showImage"});
                        showImage.children("img").attr({"src": newurl});
                        $("#editRequireDetail").find(".addImage").before(showImage);
                    }, function (error) {
                        alert("图片存储失败");
                    })
                }
            })

            $("#editRequireDetail").on("click", ".deleteImage", function (event) {          //删除图片
                event.preventDefault();
                $(this).parents(".showImage").remove();
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
                };
                $("#myCarousel").find("li").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").find(".item").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").show();
            })

            $("#background").on("click", function (event) {                       //关闭图片轮播
                event.preventDefault();
                var len = $("#myCarousel").find("li").length;
                for (var i = 0; i < len; i++) {
                    if (i == 0) {
                        $("#myCarousel").find("li").eq(0).attr({"class": ""});
                        $("#myCarousel").find(".item").eq(0).attr({"class": "item"});
                    } else {
                        $("#myCarousel").find("li").eq(1).remove();
                        $("#myCarousel").find(".item").eq(1).remove();
                    }
                }
                $("#myCarousel").hide();
            })

            $("#editRequireDetail").on("click", ".save", function (event) {      //保存编辑
                event.preventDefault();
                var requirement_id = $("#requireDetail").find(".requirement_id").text();
                var item_id = $("#" + editIndex).find(".item_id").attr("item_id");
                var mainOrder_id = $(".mainOrder").data("mainorder");
                var subOrder_id = $("#" + editIndex).parents(".detail").data("suborder");
                var item = {
                    "title": "",
                    "price": "",
                    "number": "",
                    "description": "",
                    "opnote": "",
                    "pic_urls": []
                };
                item.title = $("#editRequireDetail").find(".itemTitle").val();
                item.price = $("#editRequireDetail").find(".price").val();
                item.number = $("#editRequireDetail").find(".number").val();
                item.description = $("#editRequireDetail").find(".description").val();
                item.opnote = $("#editRequireDetail").find(".opnote").val();
                for (var i = 0; i < $("#editRequireDetail").find(".showImage").length; i++) {
                    item.pic_urls.push($("#editRequireDetail").find(".showImage").eq(i).find("img").attr("src"))
                };
                console.log(item);                                    //打印传输数据
                if (item.price == "" || item.title == "" || item.number == "") {
                    alert("信息不完整");
                    return false
                };
                if (subOrder_id == undefined) {
                    subOrder_id = "";
                };
                console.log(item_id);
                if (!item_id == "") {                   //有item_id时发送更新商品的请求，没有item_id时发送创建商品的请求
                    var url = "/operator/updateOrderItem/" + item_id + "/mainOrderId/" + mainOrder_id;
                    $.ajax({
                        url: url,
                        type: "post",
                        dataType: "json",
                        data: item,
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {                            //后台商品存储成功后在前端显示
                            if(response.status==414){
                                alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                                return false;
                            };
                            var index = $("#" + editIndex);
                            if (item.pic_urls.length) {
                                index.find(".thumb_url").attr({"src": item.pic_urls[0]});
                                index.find(".imageContainer").attr({"data-img": item.pic_urls})
                            } else {
                                index.find(".thumb_url").attr({"src": "{{url('/image/DefaultPicture.jpg')}}"});
                                index.find(".imageContainer").attr({"data-img": ""})
                            };
                            index.find(".title").text(item.title);
                            index.data("item-title",item.title);
                            index.find(".price").text(item.price);
                            index.find(".number").text(item.number);
                            index.find(".description").text(item.description);
                            index.find(".description").data("opnote", item.opnote);
                            index.find(".change").css({"color": "white"})
                            alert("保存成功");
                            $("#editRequireDetail").find(".closed").click();
                            calculate(index.parents(".detail"));
                            calculate($(".detailHeader").eq(0))
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    })
                } else {
                    $.ajax({
                        url: "/operator/createNewItem/" + mainOrder_id,
                        type: "post",
                        dataType: "json",
                        data: item,
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {                            //后台商品存储成功后在前端显示
                            if(response.status==414){
                                alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                                return false;
                            };
                            newItem = $("tbody").eq(0).clone();
                            newItem.attr({
                                "id": "tb" + $("tbody").length,
                                "draggable": "true",
                                "ondragstart": "drag(event)"
                            });
                            $(".detailHeader").find("caption").before(newItem);
                            newItem.show();
                            newItem.find(".change").show();
                            newItem.find(".deleteThis").show();
                            var index = newItem;
                            if (item.pic_urls.length) {
                                index.find(".thumb_url").attr({"src": item.pic_urls[0]});
                                index.find(".imageContainer").attr({"data-img": item.pic_urls})
                            } else {
                                index.find(".thumb_url").attr({"src": "{{url('/image/DefaultPicture.jpg')}}"});
                                index.find(".imageContainer").attr({"data-img": ""})
                            }
                            ;
                            index.find(".title").text(item.title);
                            index.data("item-title",item.title);
                            index.find(".item_id").attr({"item_id": response});
                            index.find(".price").text(item.price);
                            index.find(".number").text(item.number);
                            index.find(".description").text(item.description);
                            index.find(".description").data("opnote", item.opnote);
                            index.find(".change").css({"color": "white"});
                            alert("添加成功");
                            $("#editRequireDetail").find(".closed").click();
                            calculate($(".detailHeader").eq(0));
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    })
                }
            })

            $("#editRequireDetail").on("click", ".closed", function (event) {               //关闭编辑
                event.preventDefault();
                $("#background").slideToggle("fast");
                $("#editRequireDetail").slideToggle("fast");
                var imageNumber = $("#editRequireDetail").find(".showImage").length;
                for (var i = 0; i < imageNumber; i++) {
                    $("#editRequireDetail").find(".showImage")[0].remove();
                };
                $("#editRequireDetail").find("input").each(function () {
                    $(this).val("");
                });
                $("#editRequireDetail").find("textarea").each(function () {
                    $(this).val("");
                });
            })

            $("#divideOrder").on("click", ".deleteThis", function (event) {               //删除商品
                event.preventDefault();
                if (confirm("确认删除?")) {
                    var mainOrder_id = $(".mainOrder").data("mainorder");
                    var subOrder_id = $(this).parents(".detail").data("suborder");
                    if (subOrder_id == undefined) {
                        subOrder_id = "";
                    }
                    var item_id = $(this).parents("tbody").find(".item_id").attr("item_id");
                    var url = "/operator/deleteOrderItem/" + item_id + "/" + "mainOrderId/" + mainOrder_id + "/" + "subOrderId/" + subOrder_id;
                    var that = this;
                    $.ajax({                                             //删除商品时通知后台
                        url: url,
                        type: "get",
                        dataType: "json",
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response) {
                            if(response.status==414){
                                alert("对不起，你不是该需求的处理人。请联系原处理人或联系管理员变更需求处理人。");
                                return false;
                            };
                            if (response == 1) {
                                var parents=$(that).parents(".detail");
                                $(that).parents("tbody").remove();
                                alert("删除成功！");
                                calculate(parents);
                                calculate($(".detailHeader").eq(0))
                            } else {
                                alert("删除失败，请联系开发同学！")
                            }
                        },
                        error: function (request, errorType, errorMessage) {
                            alert("error:" + errorType + ";  message:" + errorMessage);
                        }
                    })
                }
            })

            $("#divideOrder").on("click", ".sendIndexOrder", function (event) {          //发送报价操作
                event.preventDefault();
                var saveEnable = 1;
                var that=this;
                for (var i = 0; i < $(".detail").length; i++) {
                    if ($(".detail").eq(i).find("tbody").length == 0) {
                        saveEnable = 0;
                    }
                };
                if ($("[ondragstart]").length > 1 || saveEnable == 0) {
                    alert("请处理所有商品或删除空的子订单")
                } else {
                    window.location.href = "/operator/sendPrice/" + $(".mainOrder").data("mainorder");                           //后台填写保存后的跳转地址>>>>>>> 3b0db6ea858a9aa7ac8313fb2fdcb10a367baafd
                };
            })

            $("#divideOrder").on("click",".addMemo",function(event){
                event.preventDefault();
                $("#addRemarks").find("form").attr({"action":$(this).data("url")})
            })

        })
    </script>

@stop