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
                    <th class="th1">需求/商品</th>
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

                if($requirement->operator_id==0)
                {
                    $requirementDetails = $requirement->requirementDetails;
                    $items = $requirement->items;
                    $number = 0;
                    $title = '';
                    $price = 0;
                    $img = [];

                    foreach($items as $item) {
                        $title .= $item->title . '(已保存);';
                        $number+= $item->skus()->first()->sku_inventory;
                        $img = array_merge($img, $item->pic_urls);
                        $price += $item->price * $item->skus()->first()->sku_inventory;
                    }
                    foreach ($requirementDetails as $detail) {
                        if(empty($detail->item_id)) {
                            $number += $detail->number;
                            $title .= $detail->title . '(未保存);';
                            $img = array_merge($img, $detail->pic_urls);
                        }
                    }
                    if ($requirement->state == 101) {
                        $state = '需求待领取';
                    }
                    if (mb_strlen($title, 'utf-8') > 30) {
                        $title = mb_substr($title, 0, 30) . '......';
                    }

                    $editUrl = 'operator/editRequirement/'.$requirement->requirement_number;
                    ?>

                    <tbody>
                    <tr class="separation-row"></tr>
                    <tr class="header-row">
                        <td>
                            <span class="requirement_id" id="requirementNumber"><em>NO</em>: {{$requirement->requirement_number}}</span>
                            <span class="placeholder">占</span>
                            <span class="created_at">{{$requirement->created_at}}</span>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center">
                        </td>
                    </tr>
                    <tr class="body-row">
                        <td class="title-cell clearfix" rowspan="1">
                            <span class="placeholder">占位</span>
                            <img class="thumb_url"
                                 src="{{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}}">
                            <span class="title">{{rtrim($title, ";")}}</span>
                        </td>
                        <td class="price-cell" rowspan="1">

                            <?php
                            if($price != 0) { ?>
                                <p class="price">{{'￥'.sprintf('%.2f', $price)}}</p>
                                <?php
                            }
                            ?>


                            <p><i>x{{$number}}</i><span class="total"></span></p>
                        </td>
                        <td class="country-cell" rowspan="1">
                            <p class="country_id">{{$requirement->country->name}}</p>
                        </td>
                        <td class="buyer-cell" rowspan="1">
                            <p></p>
                        </td>
                        <td class="email-cell" rowspan="1">
                            <p class="mobile">{{$requirement->user->mobile}}</p>
                            <p class="email">{{$requirement->user->email}}</p>
                            <p>{{$requirement->user->nickname}}</p>
                        </td>
                        <td class="itemStatus-cell" rowspan="1">
                            <div class="td-cont">
                                <p>{{$state}}</p>


                            </div>
                        </td>
                        <td class="edit-cell" style="text-align: center" rowspan="1">
                            <div class="td-cont">

                                <a class="btn btn-primary edit" href="{{url($editUrl)}}" role="button">领取</a>


                            </div>
                        </td>
                    </tr>
                    </tbody>
                <?php } ?>
                @endforeach
            </table>
        </div>
    </div>
    <div id="buyer" class="tab-pane fade"></div>
</div>
<nav>
    {!! $requirements->render() !!}
</nav>
<script>
    $('#requirementWaitAccept').addClass('active');
    $("#requirementMangement").addClass("active");
</script>
@stop



