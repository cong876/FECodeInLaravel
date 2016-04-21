@foreach($requirements as $requirement)
        <?php
        $requirementDetails = $requirement->requirementDetails;
        $title_json= [];
        $description_json = [];
        $totalNumber = 0;
        $img = [];
        $title = '';
        $description = '';
        $img_json= [];
        $number_json = [];
        foreach($requirementDetails as $detail)
        {
            array_push($title_json,$detail->title);
            array_push($description_json,$detail->description);
            $totalNumber += $detail->number;
            $title .= $detail->title . ';';
            $description .=$detail->description . ';';
            $img = array_merge($img, $detail->pic_urls);
            array_push($img_json,$detail->pic_urls);
            array_push($number_json,$detail->number);
        }
        $title = rtrim($title,';');
        if (mb_strlen($title, 'utf-8') > 30) {
            $title = mb_substr($title, 0, 30) . '...';
        };
        $title_json = json_encode($title_json);
        $description_json = json_encode($description_json);
        $img_json = json_encode($img_json);
        $number_json = json_encode($number_json);
        if($requirement->operator_id ==0)
            {$op_mobile = '18701133614';}
        else{$op_mobile = $requirement->operator->user->mobile;}
        ?>
        <li class="order" 
            data-item-title="{{$title_json}}"
            data-item-number="{{$number_json}}"
            data-item-description="{{$description_json}}"
            data-item-url="{{$img_json}}"
            data-item-status="0"
            data-item-created-time="{{$requirement->created_at}}"
            data-item-memos=""
            data-item-operator-mobile="{{$op_mobile}}">
            <table>
                <tr class="orderHeader">
                    <td>
                        <img src={{url("image/orderMark.png")}}>
                        <span class="country">{{$requirement->country->name}}</span>
                    </td>
                    <td colspan="2">
                        <small>需求号</small>
                        <small class="requirement_id">{{$requirement->requirement_number}}</small>
                    </td>
                </tr>
                <tr class="orderBody">
                    <td class="imgContainer">
                        <img src={{isset($img[0]) ? $img[0] : '/image/DefaultPicture.jpg'}} class="requirement_image">
                    </td>
                    <td class="requirementTitleContainer">
                        <p>{{$title}}</p>
                        <p><i><span class="totalNumber">{{$totalNumber}}</span>件商品</i></p>
                    </td>
                </tr>
                <tr class="orderFooter">
                    <td colspan="2">
                        <span class="callOp">联系客服</span>
                        <span class="cancleOrder">取消需求</span>
                    </td>
                </tr>                
            </table>
        </li>
    @endforeach
