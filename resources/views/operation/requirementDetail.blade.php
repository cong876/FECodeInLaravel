@extends("operation.partial.master")

@section('content')
    <div id="requireDetail">
        <div class="detail panel-body">
            <table class="ui-table-order">
                <thead>
                <tr>
                    <th class="thItem">商品</th>
                    <th class="thPrice">商品单价(元)</th>
                    <th class="thNumber">数量</th>
                    <th class="thDescription">备注</th>
                    <th class="thStatus">商品状态</th>
                    <th class="thCreatItem placeholder">生成商品</th>
                </tr>
                <tr class="header-row">
                    <td style="text-align: left">
                        <span class="placeholder">占</span>
                        需求号:<span class="requirement_id">{{$requirement->requirement_number}}</span>
                        <span class="placeholder">占</span>
                        <span class="created_at">{{$requirement->created_at}}</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php $url = 'operator/addRequirementMemo/'. $requirement->requirement_id; ?>
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
                <?php
                    $requirement_count = 0;
                ?>
                @foreach($requirement->requirementDetails as $detail)
                    <?php
                    if($detail->is_available == 1) {
                        $requirement_count += 1;
                    }
                    ?>
                @endforeach
                @foreach($requirement->requirementDetails as $detail)
                    @if($detail->is_available == 1)
                        <?php
                        if(!empty($detail->item_id)) {
                            $item = $detail->item;
                            $title = $item->title;
                            $images = $item->pic_urls;
                            $imgStr = implode(',',$images);
                            $img_src = isset($images[0]) ? $images[0] : '/image/DefaultPicture.jpg';
                            $state = 1;
                            $number = $item->skus()->first()->sku_inventory;
                        }else {
                            $title = $detail->title;
                            $images = $detail->pic_urls;
                            $imgStr = implode(',',$images);
                            $img_src = isset($detail->pic_urls[0]) ? $detail->pic_urls[0] : '/image/DefaultPicture.jpg';
                            $state = 0;
                            $number = $detail->number;
                        }
                        $fullTitle = $title;
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }
                        $memos = DB::table('requirement_memos')->where('requirement_id',$requirement->requirement_id)->where('is_available',1)->orderBy('created_at','desc')->get();
                        ?>


                        <tbody class="requirement" data-item-title="{{$fullTitle}}">
                        <tr class="separation-row"></tr>
                        <tr class="body-row">
                            <td class="title-cell clearfix" rowspan="1">
                                <span class="placeholder">占位</span>
                                <img class="thumb_url" src="{{$img_src}}">
                                <span data-img="{{$imgStr}}" class="imageContainer"></span>
                                <span data-detail-id="{{$detail->requirement_detail_id}}" class="detail-id"></span>
                                <?php
                                if(!empty($detail->item_id)) {
                                ?>
                                <span data-item-id="{{$detail->item_id}}" class="item-id"></span>
                                <?php

                                }
                                ?>
                                <?php
                                if(!empty($detail->item_id)) {
                                ?>
                                <span data-opnote-id="{{$item->detail_positive->hlj_admin_response_information}}" class="operatingNotes"></span>
                                <?php
                                }
                                ?>
                                <span data-detail-id="{{$detail->requirement_detail_id}}" class="detail-id"></span>
                                <span data-state="{{$detail->state}}" class="state"></span>
                                <span class="title">{{$title}}</span>
                            </td>
                            <td class="price-cell" rowspan="1">
                                <p>
                                    @if($state == 0)
                                    @else
                                        <span>￥</span>
                                    @endif
                                    <span class="price">
                                            @if($state == 0)
                                        @else
                                            {{sprintf('%.2f',$item->skus[0]->sku_price)}}
                                        @endif
                                        </span>
                                </p>
                            </td>
                            <td class="number-cell">
                                <p><i>x</i><span class="number">{{$number}}</span></p>
                            </td>
                            <td class="description-cell">
                                <p class="description">{{$detail->description}}</p>
                            </td>
                            <td class="status-cell">
                                @if($state == 0)
                                    <p class="status">未保存</p>
                                @else
                                    <p class="status saved">已保存</p>
                                @endif
                            </td>
                            <td class="change-cell">
                                <p>
                                    @if($state == 0)
                                        <a class="btn btn-primary change" href="#" role="button">生成商品</a>
                                    @else
                                        <a class="btn btn-primary change" href="#" role="button">更改</a>
                                    @endif
                                </p>
                                <?php if($requirement_count > 1) { ?>
                                <p>
                                    <a class="btn btn-danger deleteThis" href="{{url('operator/deleteItem/'.$detail['requirement_detail_id'])}}" role="button">删除</a>
                                </p>
                                <?php } ?>
                            </td>
                        </tr>
                        </tbody>



                    @endif
                @endforeach

            </table>
            <div class="alert alert-info" role="alert">
                <p>
                    <strong>买家信息</strong>
                </p>
                <p>
                    <span>手机号码：</span>
                    <span class="mobile">{{$requirement->user->mobile}}</span></br>
                    <span>邮箱：</span>
                    <span class="email">{{$requirement->user->email}}</span>
                </p>

            </div>
            <p class="buttonarea">
                <a class="btn btn-default nextstep" href="#" role="button">下一步，拆单</a>
            </p>
        </div>



        <div id="editRequireDetail">
            <a class="btn btn-danger closed" role="button">关闭</a>
            <a class="btn btn-primary save" role="button">保存</a>

            <form role="form" method="post" action="">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="pic_urls" class="image">

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
                    <textarea name="description" class="description form-control" rows="3" readonly="readyonly"></textarea>
                </div>
                <label for="operatingNotes">运营备注</label>
                <div class="form-group">
                    <textarea name="operatingNotes" class="operatingNotes form-control" placeholder="运营的同学在这里填写运营备注" rows="3"></textarea>
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
                            @if(!empty($memos))
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
                                    <?php } ?>
                                </p>
                                <p>{{$memo->content}}</p>
                            </li>
                            @endforeach
                            @endif
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
                        <form role="form" method="post" class="clearfix" action="{{url($url)}}">
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
            <a class="carousel-control left" style="line-height: 600px; font-size: 40px; background-image: none" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-circle-arrow-left" style="color: white;"></span></a>
            <a class="carousel-control right" style="line-height: 600px; font-size: 40px; background-image: none" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-circle-arrow-right" style="color: white"></span></a>
        </div>

    </div>




    <script type="text/javascript">

        $("#requirementMangement").addClass("active");
        $(document).ready(function(){

            var h=document.body.scrollHeight;
            var w=document.body.scrollWidth;
            if(h<$("#editRequireDetail").height()){
                h=$("#editRequireDetail").height()+60;
            };
            $("#background").height(h);
            $("#background").width(w);
            window.onresize=function(){
                h=document.body.scrollHeight;
                w=document.body.scrollWidth;
                if(h<$("#editRequireDetail").height()){
                    h=$("#editRequireDetail").height()+60;
                };
                $("#background").height(h);
                $("#background").width(w);
            };

            var editIndex=0;

            $("#requireDetail").on("click",".change",function(event){                  //编辑商品详情
                event.preventDefault();
                var state=$(this).parents("tbody").find(".state").data("state");
                var item_id=$(this).parents("tbody").find(".item-id").data("item-id");
                var detail_id=$(this).parents("tbody").find(".detail-id").data("detail-id");
                if(state==0){
                    $("#editRequireDetail").find("form").attr({"action":"/operator/generateItems/"+detail_id});    //这里填写创建商品的地址
                }else{;
                    $("#editRequireDetail").find("form").attr({"action":"/operator/updateItem/"+item_id+"/requirementDetailId/"+detail_id});    //这里填写更新商品的地址
                };
                editIndex=$(this).parents("tbody").prevAll("tbody").length;
                var requirementIndex=$(this).parents(".requirement");
                $("#editRequireDetail").find(".itemTitle").val(requirementIndex.data("item-title"));
                $("#editRequireDetail").find(".price").val($.trim(requirementIndex.find(".price").text()));
                $("#editRequireDetail").find(".number").val(requirementIndex.find(".number").text());
                $("#editRequireDetail").find(".description").val(requirementIndex.find(".description").text());
                $("#editRequireDetail").find(".operatingNotes").val(requirementIndex.find(".operatingNotes").data("opnote-id"))
                var imgIndex=requirementIndex.find(".imageContainer").data("img").split(",");
                if(!requirementIndex.find(".imageContainer").attr("data-img")==""){
                    for(var i=0; i<imgIndex.length; i++){
                        var showImage=$("#editRequireDetail").find(".showImageEx").clone().attr({"class":"showImage"});
                        showImage.children("img").attr({"src":imgIndex[i]});
                        $("#editRequireDetail").find(".addImage").before(showImage);
                    };
                };
                $("#background").fadeToggle("fast");
                $("#editRequireDetail").fadeToggle("fast");
            })

            $("#editRequireDetail").on("change",".chosefiles",function(){                 //上传图片
                var that=this;
                var fileUploadControl=$(this)[0];
                if($(that).parent("div").parent("div").children("div").length>11){
                    alert("最多上传9张图片");
                    return false;
                };
                if(fileUploadControl.files.length > 0){
                    var file = fileUploadControl.files[0];
                    var name =  fileUploadControl.files[0]['name'];
                    var avFile = new AV.File(name, file);
                    avFile.save().then(function(json) {
                        var newurl=json._url;     //原图片地址
                        var thumb=avFile.thumbnailURL(210, 210);    //压缩图片地址
                        var showImage=$("#editRequireDetail").find(".showImageEx").clone().attr({"class":"showImage"});
                        showImage.children("img").attr({"src":newurl});
                        $("#editRequireDetail").find(".addImage").before(showImage);
                    },function(error) {
                        alert("图片存储失败");
                    })
                }
            })

            $("#editRequireDetail").on("click",".deleteImage",function(event){          //删除图片
                event.preventDefault();
                $(this).parents(".showImage").remove();
            })

            $("#editRequireDetail").on("click","img",function(event){         //显示图片轮播
                event.preventDefault();
                event.stopImmediatePropagation();
                var that=this;
                var imageIndex=$(that).parents(".imageArea").children(".showImage");
                var imageNumber=imageIndex.length;
                for(var i=0; i<imageNumber; i++){
                    if(i==0){
                        $("#myCarousel").find(".item").children("img").attr({"src":imageIndex.eq(0).children("img").attr("src")})
                    }else{
                        var olIndex=$("#myCarousel").children(".carousel-indicators");
                        var addLi=olIndex.children("li").first().clone();
                        addLi.attr({"data-slide-to":i});
                        olIndex.append(addLi);
                        var divIndex=$("#myCarousel").children(".carousel-inner");
                        var addDiv=divIndex.children("div").first().clone();
                        addDiv.children("img").attr({"src":imageIndex.eq(i).children("img").attr("src")});
                        divIndex.append(addDiv);
                    }
                }
                $("#myCarousel").find("li").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").find(".item").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").show();
            })

            $("#background").on("click",function(event){                       //关闭图片轮播
                event.preventDefault();
                var len=$("#myCarousel").find("li").length;
                $("#myCarousel").hide();
                setTimeout(function(){
                    for(var i=0;i<len; i++){
                        if(i==0){
                            $("#myCarousel").find("li").eq(0).attr({"class":""});
                            $("#myCarousel").find(".item").eq(0).attr({"class":"item"});
                        }else{
                            $("#myCarousel").find("li").eq(1).remove();
                            $("#myCarousel").find(".item").eq(1).remove();
                        }
                    }
                },500)
            })

            $("#editRequireDetail").on("change",".price",function(event){
                event.preventDefault();
                $(this).val($(this).val()>0.01 ? parseFloat($(this).val()).toFixed(2) : 0.01);
            })

            $("#editRequireDetail").on("change",".number",function(event){
                event.preventDefault();
                $(this).val($(this).val()>1 ? parseInt($(this).val()) : 1);
            })

            $("#editRequireDetail").on("click",".save",function(event){               //保存编辑
                event.preventDefault();
                event.stopImmediatePropagation();
                var checkPrice=$(this).next("form").find(".price").val();
                var imageIndex=$(this).next("form").find(".showImage");
                var pic_urls=[];
                var that=this;
                for(var i=0; i<imageIndex.length; i++){
                    pic_urls.push(imageIndex.eq(i).find("img").attr("src"));
                };
                $(this).next("form").find(".image").val(JSON.stringify(pic_urls));
                setTimeout(function(){
                    if(!checkPrice==""){
                        $(that).next("form").submit();
                    }else{
                        alert("请填写价格")
                    }
                },1);
            })

            $("#editRequireDetail").on("click",".closed",function(event){               //关闭编辑
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#editRequireDetail").fadeToggle("fast");
                var imageNumber=$("#editRequireDetail").find(".showImage").length;
                for(var i=0; i<imageNumber; i++){
                    $("#editRequireDetail").find(".showImage")[0].remove();
                };
                var input=$("#editRequireDetail").find("input");
                var textarea=$("#editRequireDetail").find("textarea");
                for(var i=0; i<input.length; i++){
                    input.eq(i).val("")
                };
                for(var i=0; i<textarea.length; i++){
                    textarea.eq(i).val("")
                }
            })

            $("#requireDetail").on("click",".deleteThis",function(event){               //删除需求或商品
                event.preventDefault();
                var that=this;
                if(confirm("确认删除?")){
                    window.location.href=$(that).attr("href");
                }
            })

            $("#requireDetail").on("click",".nextstep",function(event){           //下一步，拆单
                event.preventDefault();
                if($(".status").length==$(".saved").length){
                    var data={
                        "totalPrice":"",
                        "requirement_id":$("#requireDetail").find(".requirement_id").text()
                    };
                    var totalPrice=0;
                    for(var i=0; i<$(".price").length; i++){
                        if(parseFloat($(".price").eq(i).text())>0){
                            totalPrice=totalPrice+parseFloat($(".price").eq(i).text());
                        }
                    };
                    data.totalPrice=totalPrice;
                    console.log(data);
                    $.ajax({
                        url:"/operator/createMain",            //下一步拆单的请求接收地址
                        type:"post",
                        dataType:"json",
                        data:data,            //需求号，红领巾ID，总价
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response){
                            window.location.href=response;
                        },
                        error: function (request,errorType,errorMessage){
                            alert("error:"+errorType+";  message:"+errorMessage);
                        }
                    })
                }else{
                    alert("请保存当前页下所有商品")
                }

            })

            $("#requireDetail").on("click",".addMemo",function(event){
                event.preventDefault();
                $("#addRemarks").find("form").attr({"action":$(this).data("url")})
            })

        })
    </script>

@stop

