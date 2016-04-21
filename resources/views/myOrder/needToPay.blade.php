@foreach($mainOrders as $mainOrder)
    <?php
    $subOrders = $mainOrder->subOrders->sortByDesc('updated_at');
    ?>
    @foreach($subOrders as $subOrder)
        <?php
        if(($subOrder->sub_order_state == 201) || ($subOrder->sub_order_state == 241))
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
        if ($sub_Order->order_type == 0)
        {
            foreach ($items as $item) {
                $number += $item->detail_positive->number;
                $title .= $item->title . ';';
                $img = array_merge($img, $item->pic_urls);
                array_push($title_json, $item->title);
                array_push($number_json, $item->detail_positive->number);
                array_push($img_json, $item->pic_urls);
                array_push($description_json, $item->detail_positive->hlj_buyer_description);
                array_push($memo_json, $item->detail_positive->hlj_admin_response_information);
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

        $op_mobile = $sub_Order->operator->user->mobile;

        if ($sub_Order->seller->name_abbreviation != '') {
            $seller_all = $sub_Order->seller->real_name;
            $seller = mb_substr($seller_all, 0, 1);
            $seller = $seller . "同学";
        } else {
            $seller = $sub_Order->seller->real_name;
        }
        $time_offer = $sub_Order->created_offer_time;
        $time_due = date('Y-m-d H:i:s', strtotime($time_offer) + 3 * 24 * 60 * 60);

        ?>
        <li class="order"
            data-item-title="{{$title_json}}"
            data-item-number="{{$number_json}}"
            data-item-description="{{$description_json}}"
            data-item-url="{{$img_json}}"
            data-item-memos="{{$memo_json}}"
            data-item-status="1"
            data-order-offer-time="{{$time_offer}}"
            data-order-ex-time="{{$time_due}}"
            data-item-seller="{{$seller}}"
            data-item-seller-url="{{url('image/Australia.jpg')}}"
            data-item-operator-mobile="{{$op_mobile}}">
            <table>
                <tr class="orderHeader">
                    <td>
                        <img src={{url("image/orderMark.png")}}>
                        <span class="country">{{$sub_Order->country->name}}</span>
                    </td>
                    <td colspan="2">
                        <small>订单号</small>
                        <small class="order_id">{{$sub_Order->sub_order_number}}</small>
                    </td>
                </tr>
                <tr class="orderBody">
                    <td class="imgContainer">
                        <img src={{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}} class="requirement_image">
                    </td>
                    <td class="orderTitleContainer">
                        <div class="orderTitleOuter">
                            <p class="orderTitle">{{$title}}</p>

                            <p>
                                <i><span class="totalNumber">{{$number}}</span>件商品</i>
                                <span class="orderPriceOuter">合计：￥<span
                                            class="orderPrice">{{$sub_Order->sub_order_price}}</span></span>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr class="orderFooter">
                    <td colspan="2">
                        <span class="payOrder">付款</span>
                        <span class="callOp">联系客服</span>
                        <span class="cancleOrder">取消订单</span>
                    </td>
                </tr>
            </table>
        </li>
        <?php
        }
        ?>
    @endforeach
@endforeach
