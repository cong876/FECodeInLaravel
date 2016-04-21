@extends("operation.partial.master")




@section('content')
    @include('operation.partial.requirementQuery')
    <div id="myTabContent" class="tab-content">
        <div id="order" class="panel panel-default tab-pane fade in active">
            @include('operation.partial.requirementPartial')
            <div class="detail panel-body">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th class="th1">订单/商品</th>
                        <th class="th2">商品总价/件数</th>
                        <th class="th3">国家</th>
                        <th class="th4">买手</th>
                        <th class="th5">买家联系方式</th>
                        <th class="th6">需求状态</th>
                        <th class="th7 placeholder">占个位置</th>
                    </tr>
                    </thead>
                    @foreach($requirements as $requirement)
                        <?php
                        if ($requirement->state == 301)
                        {
                        $state = "需求已完成";
                        $requirementFinished = $requirement;
                        $requirementDetails = $requirementFinished->requirementDetails;
                        $items = $requirementFinished->items;
                        $number = 0;
                        $title = '';
                        $img = [];
                        $price = 0;
                        if (isset($items)) {
                            foreach ($items as $item) {
                                $number += $item->skus()->first()->sku_inventory;
                                $title .= $item->title . ';';
                                $img = array_merge($img, $item->pic_urls);
                                $price += $item->skus()->first()->sku_price * $item->skus()->first()->sku_inventory;
                            }
                        }



                        if (mb_strlen($title, 'utf-8') > 30) {
                            $title = mb_substr($title, 0, 30) . '......';
                        }
                             ?>
                        {{--$goOnUrl = 'operator/splitOrder/' . $requirementFinished->main_order_id;--}}
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                        <td>
                        <span class="requirement_id" id="requirementNumber"><em>NO</em>: {{$requirement->requirement_number}}</span>
                    <span class="placeholder">占</span>
                    <span class="updated_at">{{$requirementFinished->updated_at}}</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: center">
                        <a class="requirement_shot" href="#">查看快照</a>
                        <span class="placeholder">占位</span>
                        <a class="requirement_memo" href="#">备注</a>
                    </td>
                    </tr>
                    <tr class="body-row">
                        <td class="title-cell clearfix" rowspan="1">
                            <span class="placeholder">占位</span>
                            <img class="thumb_url"
                                 src="{{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}}">
                            <span class="title">{{$title}}</span>
                        </td>
                        <td class="price-cell" rowspan="1">
                            <?php
                            if($price != 0) {
                            ?>
                            <p class="price">{{'￥'.sprintf('%.2f',$price)}}</p>
                            <?php
                            }
                            ?>


                            <p><i>x{{$number}}</i><span class="total"></span></p>
                        </td>
                        <td class="country-cell" rowspan="1">
                            <p class="country_id">{{$requirementFinished->country->name}}</p>
                        </td>
                        <td class="buyer-cell" rowspan="1">
                            <p></p>
                        </td>
                        <td class="email-cell" rowspan="1">
                            <p class="mobile">{{$requirementFinished->user->mobile}}</p>

                            <p class="email">{{$requirementFinished->user->email}}</p>
                        </td>
                        <td class="itemStatus-cell" rowspan="1">
                            <div class="td-cont">
                                <p>{{$state}}</p>
                            </div>
                        </td>
                        <td class="edit-cell" style="text-align: center" rowspan="1">
                            {{--<div class="td-cont">--}}

                            {{--<a class="btn btn-primary edit" href="#"></a>--}}


                            {{--</div>--}}
                        </td>
                    </tr>
                    </tbody>

                    <?php
                        }?>
                    @endforeach


                </table>
            </div>
        </div>
        <div id="seller" class="tab-pane fade"></div>
    </div>
    <nav>
        {!! $requirements->render() !!}
    </nav>
    <script>
        $('#requirementFinished').addClass('active');
        $("#requirementMangement").addClass("active");
    </script>

@stop
