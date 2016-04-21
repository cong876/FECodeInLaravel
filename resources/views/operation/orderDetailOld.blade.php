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
    $requirement_created_at = $requirement->created_at;
    ?>
    <div id="divideOrder">

        <div class="detailHeader panel-body" ondrop="drop2(event)" ondragover="allowDrop(event)">
            <table class="ui-table-order">
                <thead>
                <tr class="header-row">
                    <td style="text-align: left">
                        <span class="placeholder">占</span>
                        需求号:<span class="requirement_id">{{$requirement_id}}</span>
                        <span class="mainOrder" data-mainorder="{{$mainOrder->main_order_id}}"></span> <!-- 主订单id -->
                        <span class="placeholder">占</span>
                        <span class="created_at">{{$requirement->created_at}}</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td><p style="float:right">当前需求总价:￥<span
                                    class="totalPrice" >{{sprintf("%.2f", isset($mainOrder->main_order_price) ? $mainOrder->main_order_price : 0)}}</span>
                        </p></td>
                    <td></td>
                    <td></td>
                </tr>
                </thead>

                <!-- 后台渲染部分 -->
                {{--渲染未分配订单--}}
                @foreach($remain_item_ids as $remain_item_id)
                    <?php
                    $item = \App\Models\Item::find($remain_item_id);
                    $images = $item->pic_urls;
                    if (!empty($images)) {
                        $imgStr = implode(',', $images);
                    }
                    ?>
                    <tbody class="requirement" draggable="true" ondragstart="drag(event)" data-suborder="">
                    <tr class="separation-row"></tr>
                    <tr class="body-row">
                        <td class="title-cell clearfix" rowspan="1">
                            <span class="placeholder">占位</span>

                            <img class="thumb_url" src="{{isset($images[0]) ? $images[0]: ''}}">
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
                    </tr>
                    </tbody>
                    @endforeach
                            <!-- 后台渲染结束 -->
            </table>
        </div>

        {{--渲染已经拆分的订单--}}
        @foreach($suborders as $suborder)
            <?php
            $country_id = $suborder->country_id;
            $items = $suborder->items;
            ?>
            <div class="detail panel-body" data-suborder="{{$suborder->sub_order_id}}">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th class="thItem">商品</th>
                        <th class="thPrice">商品单价(元)</th>
                        <th class="thNumber">数量</th>
                        <th class="thDescription">备注</th>
                        <th class="thStatus">运营备注</th>
                        <th class="thCreatItem placeholder">生成商品</th>
                    </tr>

                    </thead>
                    @foreach($items as $item)
                        <tbody class="requirement" draggable="false" data-suborder="{{$suborder->sub_order_id}}">
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
                            <td class="status-cell">
                                <p class="status">{{$item->detail_positive->hlj_admin_response_information}}</p>
                            </td>
                        </tr>
                        </tbody>
                    @endforeach
                    <caption style="color:#0037FF" >
                <span style="float:left font-weight:bold" >子订单号: {{$suborder->sub_order_number}}</span>
                <span class="placeholder">占位</span>
                <span class="placeholder">占位</span>
                <span style="margin-left: 30px font-weight:bold">{{$suborder->updated_at}}</span>
                <span class="placeholder">占位</span>
                <span class="placeholder">占位</span>
                <span style="font-weight:bold">国家: {{$suborder->country->name}}</span>
                <span class="placeholder">占位</span>
                <span class="placeholder">占位</span>
                <span style="margin-left: 20px font-weight:bold">买手: 大萌</span>
                <span class="placeholder">占位</span>
                <span class="placeholder">占位</span>
                <span style="margin-left: 50px font-weight:bold">邮费: {{$suborder->postage}}</span>
                <span class="placeholder">占位</span>
                <span><p style="float:right">子订单总价:￥<span class="totalPrice">{{
                        sprintf('%.2f',$suborder->sub_order_price)}}</span></p></span>
                 {{--</table>--}}
                    </caption>
                </table>
            </div>

        @endforeach



        <div class="alert alert-info detailFooter" role="alert">
            <strong>买家信息</strong><br/>
            <span style="font-weight: bold">手机号码：</span>
            <span class="mobile" style="font-weight: bold">{{$user->mobile}}</span>
            <span style="font-weight: bold">邮箱：</span>
            <span class="email" style="font-weight: bold">{{$user->email}}</span>
        </div>

        <div class="panel-body">
            <p class="buttonarea">
                {{--<a class="btn btn-default saveIndexOrder" role="button" style="width: 120px;">保存，不发送</a>--}}
                <a class="btn btn-default sendIndexOrder" role="button" style="width: 120px;">发送报价</a>
            </p>
        </div>
@stop