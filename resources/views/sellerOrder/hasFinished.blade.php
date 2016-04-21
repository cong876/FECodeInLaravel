<?php
use Illuminate\Support\Facades\Cache;
$subOrders = $subOrders->sortByDesc('updated_at');
$regionInstance = \App\Helper\ChinaRegionsHelper::getInstance();
?>
@foreach($subOrders as $subOrder)
    <?php
    if($subOrder->sub_order_state == 301)
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
    if (count(Cache::get('suborder:' . $subOrder->sub_order_id . ':additional')) != 0) {
        if ($sub_Order->delivery_info_id != 0) {
            $cache_info = unserialize(Cache::get('suborder:' . $subOrder->sub_order_id . ':additional'));
            $delivery_number = $cache_info['delivery_order_number'];
            $delivery_url = $cache_info['delivery_related_url'];
            if ($cache_info['delivery_company_id'] != 0) {
                $company_id = $cache_info['delivery_company_id'];
                $delivery_company = App\Models\DeliveryCompany::find($company_id)->company_name;
            } else {
                $delivery_company = $cache_info['delivery_company_info'];
            }
        } else {
            $delivery_number = "无";
            $delivery_url = "";
            $delivery_company = "无";
        }
    } else {
        if ($sub_Order->delivery_info_id != 0) {
            $delivery_number = $sub_Order->deliveryInfo->delivery_order_number;
            $delivery_url = $sub_Order->deliveryInfo->delivery_related_url;
            if (($sub_Order->deliveryInfo->delivery_company_id) == 0) {
                $delivery_company = $sub_Order->deliveryInfo->delivery_company_info;
            } else {
                $delivery_company = $sub_Order->deliveryInfo->deliveryCompany->company_name;
            }
        } else {
            $delivery_number = "无";
            $delivery_url = "";
            $delivery_company = "无";
        }
    }
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
    $time_offer = $sub_Order->created_offer_time;
    $time_pay = $sub_Order->payment_time;
    $time_delivery = $sub_Order->delivery_time;
    $time_audit = $sub_Order->audit_passed_time;
    $time_finish = $sub_Order->updated_at;
    ?>
    <li class="order" data-item-title="{{$title_json}}" data-item-number="{{$number_json}}"
        data-item-description="{{$description_json}}" data-item-url="{{$img_json}}"
        data-item-memos="{{$memo_json}}" data-item-status="4" data-order-offer-time="{{$time_offer}}"
        data-order-pay-time="{{$time_pay}}" data-order-send-time="{{$time_delivery}}"
        data-order-completed-time="{{$time_finish}}" data-order-audit-time="{{$time_audit}}"
        data-item-seller="{{$seller}}"
        data-item-seller-url="{{url('image/goodbye.gif')}}"
        data-item-receiving-address="{{$province_level}}{{$city_level}}{{$county_level}}{{$street_address}}，{{$sub_Order->receivingAddress->receiver_zip_code}}"
        data-item-receiver-name="{{$sub_Order->receivingAddress->receiver_name}}"
        data-item-receiver-mobile="{{$sub_Order->receivingAddress->receiver_mobile}}"
        data-item-operator-mobile="{{$op_mobile}}" data-item-price="{{$price}}"
        data-order-postage="{{$sub_Order->postage}}"
        data-delivery-number="{{$delivery_number}}"
        data-delivery-company="{{$delivery_company}}">
        <table>
            <tr class="orderHeader">
                <td colspan="2">
                    <span><small>订单号</small></span>
                    <small class="order_id">{{$sub_Order->sub_order_number}}</small>
                    <span>已完成</span>
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
                                        class="orderPrice">{{$sub_Order->transfer_price}}</span></span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr class="orderFooter">
                <td colspan="2">
                    <span class="callOp">联系客服</span>
                    <?php if($delivery_url != "") { ?>
                    <span class="checkLogitic"><a
                                href="{{$delivery_url}}&callbackurl=http://www.yeyetech.net/seller/management">查看物流</a></span>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </li>
    <?php
    }
    ?>
@endforeach
