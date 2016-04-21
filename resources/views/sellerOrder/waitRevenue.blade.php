<?php
$subOrders = $subOrders->sortByDesc('updated_at');
$regionInstance = \App\Helper\ChinaRegionsHelper::getInstance();
?>
@foreach($subOrders as $subOrder)
    <?php
    if($subOrder->sub_order_state == 501)
    {
    $sub_Order = $subOrder;
    $items = $sub_Order->items;
    $number = 0;
    $title = '';
    $title_json = [];
    $number_json = [];
    $img = [];
    $img_json = [];
    $description_json = [];
    $memo_json = [];
    $price = [];
    if ($sub_Order->order_type == 0) {
        foreach ($items as $item) {
            $number += $item->skus->first()->sku_inventory;
            $title .= $item->title . ';';
            $img = array_merge($img, $item->pic_urls);
            array_push($title_json, $item->title);
            array_push($number_json, $item->skus->first()->sku_inventory);
            array_push($img_json, $item->pic_urls);
            array_push($description_json, $item->detail_positive->hlj_buyer_description);
            array_push($memo_json, $item->detail_positive->hlj_admin_response_information);
            array_push($price, $item->price);
        }
    }
    else
    {
        foreach ($items as $item) {
            $info_trans = \App\Models\GroupItem::where('sub_order_id', $sub_Order->sub_order_id)->first();
            $number += $info_trans->number;
            $title .= $item->title . ';';
            $img = array_merge($img, $item->pic_urls);
            array_push($title_json, $item->title);
            array_push($number_json, $info_trans->number);
            array_push($img_json, $item->pic_urls);
            array_push($description_json, $info_trans->memo);
            array_push($memo_json, "");
            array_push($price, $item->price);
        }
    }
    $title = rtrim($title, ';');
    if (mb_strlen($title, 'utf-8') > 30) {
        $title = mb_substr($title, 0, 30) . '...';
    };
    $title_json = json_encode($title_json);
    $number_json = json_encode($number_json);
    $img_json = json_encode($img_json);
    $description_json = json_encode($description_json);
    $memo_json = json_encode($memo_json);
    $price = json_encode($price);

    $op_mobile = $sub_Order->operator->user->mobile;

    $province_code = $sub_Order->receivingAddress->first_class_area;
    $city_code = $sub_Order->receivingAddress->second_class_area;
    $county_code = $sub_Order->receivingAddress->third_class_area;
    $street_address = $sub_Order->receivingAddress->street_address;
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
    $seller = $sub_Order->seller->real_name;
    $time_offer = $sub_Order->created_offer_time;
    $time_pay = $sub_Order->payment_time; ?>
    <li class="order waitSend"
        data-item-title="{{$title_json}}"
        data-item-number="{{$number_json}}"
        data-item-description="{{$description_json}}"
        data-item-url="{{$img_json}}"
        data-item-memos="{{$memo_json}}"
        data-item-status="2"
        data-order-offer-time="{{$time_offer}}"
        data-order-pay-time="{{$time_pay}}"
        data-item-seller="{{$seller}}"
        data-item-seller-url="{{url('image/America.jpg')}}"
        data-item-receiving-address="{{$province_level}}{{$city_level}}{{$county_level}}{{$street_address}}，{{$sub_Order->receivingAddress->receiver_zip_code}}"
        data-item-receiver-name="{{$sub_Order->receivingAddress->receiver_name}}"
        data-item-receiver-mobile="{{$sub_Order->receivingAddress->receiver_mobile}}"
        data-item-operator-mobile="{{$op_mobile}}"
        data-item-price="{{$price}}"
        data-order-postage="{{$sub_Order->postage}}">
        <table>
            <tr class="orderHeader">
                <td colspan="2">
                    <span><small>订单号</small></span>
                    <small class="order_id">{{$sub_Order->sub_order_number}}</small>
                    <span>请发货</span>
                </td>
            </tr>
            <tr class="orderBody">
                <td class="imgContainer">
                    <img src={{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}} class="requirement_image">
                </td>
                <td class="orderTitleContainer">
                    <div class="orderTitleOuter">
                        <p>{{$title}}</p>

                        <p>
                            <i><span class="totalNumber">{{$number}}</span>件商品</i>
                            <span class="orderPriceOuter">合计：￥<span
                                        class="orderPrice">{{$sub_Order->sub_order_price-$sub_Order->refund_price}}</span></span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr class="orderFooter">
                <td colspan="2">
                    <span class="send">发货</span>
                    <span class="cancleOrder">残忍拒绝</span>
                    <span class="callOp">联系客服</span>
                </td>
            </tr>
        </table>
    </li>
    <?php
    }
    ?>
@endforeach
<?php $subOrders = $subOrders->sortByDesc('updated_at')?>
@foreach($subOrders as $subOrder)
    <?php
    if($subOrder->sub_order_state == 521)
    {
    $sub_Order = $subOrder;
    $items = $sub_Order->items;
    $number = 0;
    $title = '';
    $title_json = [];
    $number_json = [];
    $img = [];
    $img_json = [];
    $description_json = [];
    $memo_json = [];
    $price = [];
    if ($sub_Order->order_type == 0)
    {
        foreach ($items as $item) {
            $number += $item->skus->first()->sku_inventory;
            $title .= $item->title . ';';
            $img = array_merge($img, $item->pic_urls);
            array_push($title_json, $item->title);
            array_push($number_json, $item->skus->first()->sku_inventory);
            array_push($img_json, $item->pic_urls);
            array_push($description_json, $item->detail_positive->hlj_buyer_description);
            array_push($memo_json, $item->detail_positive->hlj_admin_response_information);
            array_push($price, $item->price);
        }
    }
    else
    {
        foreach ($items as $item) {
            $info_trans = \App\Models\GroupItem::where('sub_order_id',$sub_Order->sub_order_id)->first();
            $number += $info_trans->number;
            $title .= $item->title . ';';
            $img = array_merge($img, $item->pic_urls);
            array_push($title_json, $item->title);
            array_push($number_json, $info_trans->number);
            array_push($img_json, $item->pic_urls);
            array_push($description_json, $info_trans->memo);
            array_push($memo_json, "");
            array_push($price, $item->price);
        }
    }
    $title = rtrim($title, ';');
    if (mb_strlen($title, 'utf-8') > 30) {
        $title = mb_substr($title, 0, 30) . '...';
    };
    $title_json = json_encode($title_json);
    $number_json = json_encode($number_json);
    $img_json = json_encode($img_json);
    $description_json = json_encode($description_json);
    $memo_json = json_encode($memo_json);
    $price = json_encode($price);

    $op_mobile = $sub_Order->operator->user->mobile;

    $province_code = $sub_Order->receivingAddress->first_class_area;
    $city_code = $sub_Order->receivingAddress->second_class_area;
    $county_code = $sub_Order->receivingAddress->third_class_area;
    $street_address = $sub_Order->receivingAddress->street_address;
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
    $seller = $sub_Order->seller->real_name;
    $time = $sub_Order->created_offer_time;
    $time_due = $sub_Order->payment_time; ?>
    <li class="order auditing" data-item-title="{{$title_json}}" data-item-number="{{$number_json}}"
        data-item-description="{{$description_json}}" data-item-url="{{$img_json}}" data-item-memos="{{$memo_json}}"
        data-item-status="2" data-order-offer-time="{{$time}}" data-order-pay-time="{{$time_due}}"
        data-item-seller="{{$seller}}" data-item-seller-url="{{url('image/America.jpg')}}"
        data-item-receiving-address="{{$province_level}}{{$city_level}}{{$county_level}}{{$street_address}} {{$sub_Order->receivingAddress->receiver_zip_code}}"
        data-item-receiver-name="{{$sub_Order->receivingAddress->receiver_name}}"
        data-item-receiver-mobile="{{$sub_Order->receivingAddress->receiver_mobile}}"
        data-item-operator-mobile="{{$op_mobile}}" data-item-price="{{$price}}"
        data-order-postage="{{$sub_Order->postage}}">
        <table>
            <tr class="orderHeader">
                <td colspan="2">
                    <span><small>订单号</small></span>
                    <small class="order_id">{{$sub_Order->sub_order_number}}</small>
                    <span>审核中</span>
                </td>
            </tr>
            <tr class="orderBody">
                <td class="imgContainer">
                    <img src={{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}} class="requirement_image">
                </td>
                <td class="orderTitleContainer">
                    <div class="orderTitleOuter">
                        <p>{{$title}}</p>

                        <p>
                            <i><span class="totalNumber">{{$number}}</span>件商品</i>
                            <span class="orderPriceOuter">合计：￥<span
                                        class="orderPrice">{{$sub_Order->sub_order_price-$sub_Order->refund_price}}</span></span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr class="orderFooter">
                <td colspan="2">
                    <span class="callOp">联系客服</span>
                </td>
            </tr>
        </table>
    </li>
    <?php
    }
    ?>
@endforeach
