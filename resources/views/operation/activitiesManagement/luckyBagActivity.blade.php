@extends("operation.partial.master")




@section('content')

    <div id="activityDetail" class="panel panel-default tab-pane fade in active"
         style="width: 50%; position: relative; margin: 0 auto">
        <div class="detail panel-body" style="max-height: 600px; overflow-y: auto;">
            <h4>商品
            <span>
                <a class="btn-xs btn-success" role="button" style="padding: 5px"
                   data-toggle="modal"
                   data-target="#addItem">新建商品</a>
            </span>
            </h4>
            <section>
                <ol id="itemList">
                    @foreach($items as $item)
                        <?php
                        $title = $item->title;
                        $description = $item->detail_passive->description;
                        $meta = json_decode($item->attributes);
                        $order_info = $meta->activity_meta;
                        $market_price = $order_info->market_price;
                        $seller_id = $order_info->seller_id;
                        $operator_id = $order_info->operator_id;
                        $postage = $order_info->postage;
                        $country_id = $item->country_id;
                        $price = $item->price;
                        $inventory = $item->skus()->first()->sku_inventory;
                        $pic_url = $item->pic_urls;
                        $sellers = App\Models\Seller::where('country_id', $country_id)->where('is_available', 'true')->get();
                        $seller_show = App\Models\Seller::where('seller_id', $seller_id)->first();
                        $sellers_trans = DB::table('sellers')->where('country_id', $country_id)->select('seller_id', 'real_name')->get();
                        $sellers_json = json_encode($sellers_trans);
                        $item_info = ['title' => $title, 'description' => $description, 'price' => $price, 'marketPrice' => $market_price, 'postage' => $postage,
                                'seller' => $seller_id, 'editor' => $operator_id, 'country' => $country_id, 'inventory' => $inventory, 'pic_url' => $pic_url, 'id' => $item->item_id];
                        $item_json = json_encode($item_info);
                        ?>
                        <li data-item="{{$item_json}}" data-sellers="{{$sellers_json}}">
                            <section>
                                <span class="itemTitle">{{$item->title}}</span>
                        <span class="right">
                            @if($item->is_on_shelf == false)
                                <a class="publish" href="">发布</a>
                            @else
                                <a class="cancel_publish" href="" style="color:red;font-weight:bolder;">取消发布</a>
                            @endif
                            <span class="placeholder">0</span>
                            <a class="editItem" href=""
                               data-toggle="modal"
                               data-target="#addItem">编辑</a>
                            <span class="placeholder">0</span>
                        </span>
                            </section>
                        </li>
                        </br>
                    @endforeach
                </ol>
            </section>
        </div>

        <div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog" style="width: 600px">
                <div class="modal-content">
                    <div class="modal-header" style="text-align: right; padding: 5px 15px">
                        <button type="button" class="btn btn-primary save">保存</button>
                        <span class="placeholder">0</span>
                        <button type="button" class="btn btn-danger deleteItem">删除</button>
                        <span class="placeholder">0</span>
                        <button type="button" class="btn btn-danger closeM" data-dismiss="modal" aria-hidden="true">关闭
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" method="post" class="clearfix form-horizontal" action="">
                            <label for="itemTitle">商品名称</label>
                            <input type="text" name="itemTitle" class="form-control itemTitle" placeholder="不超过16个字"/>
                            <label for="itemDescription">商品介绍</label>
                        <textarea name="itemDescription" class="itemDescription form-control"
                                  placeholder="填写商品介绍"></textarea>

                            <div class="col-lg-12 col-md-12" style="padding: 0">
                                <div class="col-lg-4 col-md-4" style="padding-left:0">
                                    <label for="itemPrice">价格</label>

                                    <div class="input-group">
                                        <input type="number" name="itemPrice" class="form-control itemPrice">
                                        <span class="input-group-addon">RMB</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4" style="padding-left:0">
                                    <label for="postage">邮费</label>

                                    <div class="input-group">
                                        <input type="number" name="postage" class="form-control postage">
                                        <span class="input-group-addon">RMB</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4" style="padding-right: 0">
                                    <label for="marketPrice">市场价</label>

                                    <div class="input-group">
                                        <input type="number" name="marketPrice" class="form-control marketPrice">
                                        <span class="input-group-addon">RMB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12" style="padding: 0">
                                <div class="col-lg-6 col-md-6" style="padding-left:0">
                                    <label for="sellerCountry">买手国家</label>
                                    <?php
                                    $countries = App\Models\Country::all();
                                    $employees = App\Models\Employee::all();
                                    ?>
                                    <div class="input-group col-lg-12 col-md-12">
                                        <select class="form-control sellerCountry" name="sellerCountry">
                                            <option>--请选择--</option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->country_id}}">{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6" style="padding-right: 0">
                                    <label for="seller">买手</label>

                                    <div class="input-group col-lg-12 col-md-12">
                                        <select class="form-control seller" name="seller">
                                            <option>--请选择--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6" style="padding-left: 0">
                                <label for="editor">处理人</label>
                                <select name="editor" class="form-control editor">
                                    @foreach($employees as $employee)
                                        <option value="{{$employee->employee_id}}">{{$employee->real_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-12 col-md-12" style="padding: 0">
                                <div class="col-lg-6 col-md-6" style="padding-left: 0">
                                    <label for="inventory">库存</label>

                                    <div class="input-group">
                                        <input type="number" name="inventory" class="form-control inventory">
                                        <span class="input-group-addon">件</span>
                                    </div>
                                </div>
                            </div>
                            <div class="itemImage">
                                <div class="showImageEx">
                                    <img src="" class="pic_urls">
                                    <a class="deleteImage" href="">×</a>
                                </div>
                                <div class="addImage">
                                    <input type="file" class="chosefiles" name="itemImage">
                                    <span class="addicon">+</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <p>商品图片长宽比为1:1（大小不要超过30kb）</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <li id="itemEx" data-item="" data-sellers="">
            <section>
                <span class="itemTitle"></span>
            <span class="right">
                <a class="publish" href="">发布</a>
                <span class="placeholder">0</span>
                <a class="editItem" href=""
                   data-toggle="modal"
                   data-target="#addItem">编辑</a>
                <span class="placeholder">0</span>
            </span>
            </section>
        </li>
    </div>

    <script type="text/javascript">
        $("#activitesMangement").addClass("active");

        $(document).ready(function(){
            var createItem = function (item) {                                                  //创建商品函数
                $.ajax({
                    url: "/operator/createLuckyBagItem",
                    type: "post",
                    dataType: "json",
                    data: item,
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response) {                                              //后台商品存储成功后在前端显示
                        if (response) {
                            item.id = response;
                            var sellers = [];
                            for (var i = 0; i < $("#addItem").find(".seller").find("option").length - 1; i++) {
                                var seller = $("#addItem").find(".seller").find("option").eq(i + 1);
                                var sellerIndex = {
                                    real_name: seller.text(),
                                    seller_id: seller.attr("value")
                                };
                                sellers.push(sellerIndex);
                            };
                            console.log(sellers);
                            var newItem = $("#itemEx").clone();
                            newItem.removeAttr("id");
                            newItem.find(".itemTitle").text(item.title);
                            newItem.data("item", item);
                            newItem.data("sellers", sellers);
                            $("#itemList").append(newItem);
                            $("#addItem").find(".closeM").click();
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                })
            }

            var updateItem = function (item) {                                                  //更新商品函数
                $.ajax({
                    url: "/operator/updateLuckyBagItem",
                    type: "get",
                    dataType: "json",
                    data: item,
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response) {                                              //后台商品存储成功后在前端显示
                        if(response){
                            var sellers=[];
                            for(var i=0; i<$("#addItem").find(".seller").find("option").length-1; i++){
                                var seller=$("#addItem").find(".seller").find("option").eq(i+1);
                                var sellerIndex={
                                    real_name: seller.text(),
                                    seller_id: seller.attr("value")
                                };
                                sellers.push(sellerIndex);
                            };
                            $("#itemIndex").find(".itemTitle").text(item.title);
                            $("#itemIndex").data("item",item);
                            $("#itemIndex").data("sellers",sellers);
                            $("#addItem").find(".closeM").click();
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                });
            }

            var deleteItem = function (item) {                                                  //删除商品函数
                $.ajax({
                    url: "/operator/deleteLuckyBagItem/" + item.data("item").id,
                    type: "get",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response) {                                              //后台商品存储成功后在前端显示
                        if (response) {
                            item.remove();
                            $("#addItem").find(".closeM").click();
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                });
            }

            var subStrByByte = function (str, limit) {
                var newStr="";
                var len=0;
                for(var i=0; i<str.length; i++){
                    if((/[^\x00-\xff]/g).test(str[i])){
                        len +=2;
                    }else{
                        len +=1;
                    };
                    if(len>limit){
                        newStr=str.substr(0,i);
                        return newStr;
                    };
                };
                return str;
            }

            $("#itemList").on("click",".publish", function (event) {
                event.preventDefault();
                var that=this;
                $.ajax({
                    url: "/operator/publishLuckyBagItem/"+$(that).parents("li").data("item").id,
                    type: "get",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response) {                                              //后台商品存储成功后在前端显示
                        if (response) {
                            alert("发布成功");
                            $(that).attr({"class":"cancel_publish","style":"color:red;font-weight:bolder;"});
                            $(that).text("取消发布");
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                });
            });

            $("#itemList").on("click",".cancel_publish", function (event) {
                event.preventDefault();
                var that=this;
                if(!confirm("确认撤销发布么？")) return false;
                $.ajax({
                    url: "/operator/cancelPublishLuckyBagItem/"+$(that).parents("li").data("item").id,
                    type: "get",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response) {                                              //后台商品存储成功后在前端显示
                        if (response) {
                            alert("撤销发布成功");
                            $(that).attr({"class":"publish"});
                            $(that).removeAttr("style");
                            $(that).text("发布");
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                });
            });

            $("#activityDetail").on("click", ".editItem", function (event) {                    //编辑商品
                event.preventDefault();
                $(this).parents("li").attr({"id": "itemIndex"});
                var item = $(this).parents("li").data("item");
                var sellers = $(this).parents("li").data("sellers");
                for (var i = 0; i < sellers.length; i++) {
                    $("#addItem").find(".seller").append("<option value='" + sellers[i].seller_id + "'>" + sellers[i].real_name + "</option>")
                };
                $("#addItem").find(".itemTitle").val(item.title);
                $("#addItem").find(".itemDescription").val(item.description);
                $("#addItem").find(".itemPrice").val(item.price);
                $("#addItem").find(".postage").val(item.postage);
                $("#addItem").find(".marketPrice").val(item.marketPrice);
                $("#addItem").find(".sellerCountry").find("option").eq(item.country).attr({"selected": "selected"});
                $("#addItem").find(".seller").find("[value='" + item.seller + "']").attr({"selected": "selected"});
                $("#addItem").find(".editor").find("[value='" + item.editor + "']").attr({"selected": "selected"});
                $("#addItem").find(".inventory").val(item.inventory);
                $("#addItem").find(".showImageEx").attr({"class": "showImage"});
                $("#addItem").find(".showImage").find("img").attr({"src": item.pic_url});
                $("#addItem").find(".addImage").hide();
            })

            $("#addItem").on("change", ".sellerCountry", function (event) {                     //选国家加载买手
                event.preventDefault();
                event.stopImmediatePropagation();
                var that = this;
                $.ajax({
                    url: "/operator/getBuyer/" + $(that).val(),
                    type: "get",
                    dataType: "json",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                        ;
                    },
                    success: function (response) {
                        $("#addItem").find(".seller").html("<option>--请选择--</option>");
                        for (var i = 0; i < response[0].length; i++) {
                            var newSeller = $("<option></option>");
                            newSeller.attr({
                                "value": response[0][i],
                                "data-pingyin": response[2][i],
                                "data-abbreviation": response[3][i]
                            });
                            newSeller.text(response[1][i]);
                            $("#addItem").find(".seller").append(newSeller);
                        }
                    },
                    error: function (request, errorType, errorMessage) {
                        alert("error:" + errorType + ";  message:" + errorMessage);
                    }
                })
            })

            $("#addItem").on("change",".itemTitle,.itemDescription",function (event) {                           //限制标题长度
                event.preventDefault();
                var that=this;
                $(that).val(subStrByByte($(that).val(),32));
            })

            $("#addItem").on("change",".marketPrice",function (event) {                         //限制输入数字
                event.preventDefault();
                $(this).val($(this).val()>0.01 ? parseFloat($(this).val()).toFixed(2) : 0.01);
            })
            $("#addItem").on("change",".itemPrice",function (event) {                           //限制输入数字
                event.preventDefault();
                $(this).val($(this).val()>1 ? parseInt($(this).val()) : 1);
            })
            $("#addItem").on("change",".postage",function (event) {                           //限制输入数字
                event.preventDefault();
                $(this).val($(this).val()>1 ? parseInt($(this).val()).toFixed(2) : 1);
            })
            $("#addItem").on("change",".inventory",function (event) {
                event.preventDefault();
                $(this).val($(this).val()>1 ? parseInt($(this).val()) : 1);
            })

            $("#activityDetail").on("change", ".chosefiles", function () {                      //上传图片
                var that = this;
                var fileUploadControl = $(this)[0];
                if (fileUploadControl.files.length > 0) {
                    var file = fileUploadControl.files[0];
                    var name = fileUploadControl.files[0]['name'];
                    var avFile = new AV.File(name, file);
                    avFile.save().then(function (json) {
                        var newurl = json._url;                                                 //原图片地址
                        var thumb = avFile.thumbnailURL(210, 210);                              //压缩图片地址
                        var imageArea = $(that).attr("name");
                        $("."+imageArea).find("img").attr({"src":newurl});
                        $("."+imageArea).find(".showImageEx").attr({"class":"showImage"});
                        $(that).parents(".addImage").hide();
                    }, function (error) {
                        alert("图片存储失败");
                    })
                }
            })

            $("#addItem").on("click", ".deleteImage", function (event) {             //删除图片
                event.preventDefault();
                $(this).parents(".showImage").next("div").show();
                $(this).parents(".showImage").attr({"class": "showImageEx"});
            })

            $("#addItem").on("click",".save",function (event) {                                 //保存商品
                event.preventDefault();
                var item = {
                    title: $("#addItem").find(".itemTitle").val(),
                    description: $("#addItem").find(".itemDescription").val(),
                    price: $("#addItem").find(".itemPrice").val(),
                    postage: $("#addItem").find(".postage").val(),
                    marketPrice: $("#addItem").find(".marketPrice").val(),
                    country: $("#addItem").find(".sellerCountry").val(),
                    seller: $("#addItem").find(".seller").val(),
                    editor: $("#addItem").find(".editor").val(),
                    inventory: $("#addItem").find(".inventory").val(),
                    pic_url: $("#addItem").find(".showImage").find("img").attr("src")
                };
                console.log(item);
                var key;
                for( key in item){
                    if(item[key] == undefined){
                        alert("请编辑完成的商品信息");
                        return false;
                    };
                    if((item[key]).toString().trim() == ""){
                        alert("请填写完整的商品信息");
                        return false;
                    };
                };
                if(item.seller == "--请选择--"){
                    alert("请选择买手");
                    return false;
                };
                if($("#itemIndex").length === 1){
                    item.id=$("#itemIndex").data("item").id;
                    updateItem(item);
                }else{
                    createItem(item);
                };
            })

            $("#addItem").on("click",".deleteItem",function (event) {                           //删除商品
                event.preventDefault();
                if($("#itemIndex").length === 1){
                    deleteItem($("#itemIndex"));
                };
            })

            $("#addItem").on("hide.bs.modal", function (event) {                                //编辑商品弹窗关闭时清理数据
                $("#addItem").find("input").val("");
                $("#addItem").find("textarea").val("");
                $("#addItem").find(".inventory").val("");
                $("#addItem").find("[selected='selected']").removeAttr("selected");
                $("#addItem").find(".sellerCountry").find("option").eq(0).attr({"selected":"selected"});
                $("#addItem").find(".seller").html("<option>--请选择--</option>");
                $("#addItem").find(".showImage").find("img").attr({"src":""});
                $("#addItem").find(".showImage").attr({"class":"showImageEx"});
                $("#addItem").find(".addImage").show();
                $("#itemIndex").removeAttr("id");
            })

        });
    </script>

@stop