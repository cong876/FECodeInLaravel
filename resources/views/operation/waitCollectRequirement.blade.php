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
                    <td></td>
                </tr>
                </thead>


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
                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }
                        ?>


                        <tbody class="requirement">
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
                                        <a class="btn btn-primary change" role="button">生成商品</a>
                                    @else
                                        <a class="btn btn-primary change" role="button">更改</a>
                                    @endif
                                </p>
                                <p>
                                    <a class="btn btn-danger deleteThis" role="button">删除</a>
                                </p>
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
                <a class="btn btn-default nextstep" role="button">下一步，拆单</a>
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
                    <input type="text" name="price" class="form-control price">
                    <span class="input-group-addon">RMB</span>
                </div>
                <label for="number">商品数量</label>
                <div class="input-group" style="padding:0 50% 0 0">
                    <input type="text" name="number" class="form-control number">
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

    <div id="collectOrNot">
        <div>
            <a class="btn btn-default sureToCollect" role="button">确定领取</a>
            <a class="btn btn-default cancle" role="button">取消</a>
        </div>
    </div>




    <script type="text/javascript">

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
            history.replaceState({},"","/operator/waitAccept");

            $("#collectOrNot").on("click",".sureToCollect",function(event){
                event.preventDefault();
                event.stopImmediatePropagation();
                window.location.href="/operator/acceptRequirement/"+$(".requirement_id").text();
            })

            $("#collectOrNot").on("click",".cancle",function(event){
                window.location.href="/operator/waitResponse";
            })


        })
    </script>

@stop
