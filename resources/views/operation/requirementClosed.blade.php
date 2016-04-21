@extends("operation.partial.master")




@section('content')
    @include('operation.partial.requirementQuery')
    @include('operation.partial.checkRemarksModal')
    
    <div id="myTabContent" class="tab-content">
        <div id="order" class="panel panel-default tab-pane fade in active">
            @include('operation.partial.requirementPartial')
            <div class="detail panel-body">
                <table class="ui-table-order">
                    <thead>
                    <tr>
                        <th class="th1">订单/商品</th>
                        <th class="th2">商品件数</th>
                        <th class="th3">国家</th>
                        <th class="th4">备注</th>
                        <th class="th5">买家联系方式</th>
                        <th class="th6">需求状态</th>
                        <th class="th7 placeholder">占个位置</th>
                    </tr>
                    </thead>
                    @foreach($requirements as $requirement)
                        <?php
                        if (($requirement->state == 411) || ($requirement->state == 431))  {
                            if ($requirement->state == 411) {
                                $state = "买家关闭";
                            } elseif ($requirement->state == 431) {
                                $state = "运营关闭";
                            }
                            $memos = $requirement->requirementMemos;
                            $memo_temp = [];
                            foreach($memos as $memo)
                            {
                                $name = App\Models\User::find($memo->hlj_id)->employee->real_name;
                                $time_memo = $memo->created_at;
                                $content = $memo->content;
                                array_push($memo_temp,array($time_memo,$content,$name));
                            }
                            $memo_json = json_encode($memo_temp);
                            $requirementFinished = $requirement;
                            $requirementDetails = $requirementFinished->requirementDetails;
                            $number = 0;
                            $title = '';
                            $img = [];
                            $description = '';
                            if (!empty($requirementDetails)) {
                                foreach ($requirementDetails as $requirementDetail) {
                                    $number += $requirementDetail->number;
                                    $title .= $requirementDetail->title . ';';
                                    $img = array_merge($img, $requirementDetail->pic_urls);
                                    $description .= $requirementDetail->description . ';';
                                }
                            }
                            if (mb_strlen($title, 'utf-8') > 30) {
                                $title = mb_substr($title, 0, 30) . '......';
                            } else {
                                $title = rtrim($title, ';');
                            }
                        }?>

                        {{--$goOnUrl = 'operator/splitOrder/' . $requirementFinished->main_order_id;--}}
                        <tbody>
                        <tr class="separation-row"></tr>
                        <tr class="header-row">
                            <td>
                                <span class="requirement_id"
                                      id="requirementNumber"><em>NO</em>: {{$requirementFinished->requirement_number}}</span>
                                <span class="placeholder">占</span>
                                <span class="updated_at">{{$requirementFinished->updated_at}}</span>
                            </td>
                            <td>
                                <span>处理人:</span>
                                <?php if($requirementFinished->operator_id != 0){ ?>
                                <span class="created_at">{{$requirementFinished->operator->real_name}}</span>
                                <?php } else { ?>
                                <span class="created_at">无</span>
                                <?php } ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: center">
                                <a class="requirement_shot placeholder" href="#">查看快照</a>
                                <span class="placeholder">占位</span>
                                <a class="requirement_memo" 
                                    data-toggle="modal" 
                                    data-target="#checkRemarks" 
                                    data-memo="{{$memo_json}}"
                                    href="">备注</a>
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
                                <p><i>x{{$number}}</i><span class="total"></span></p>
                            </td>
                            <td class="country-cell" rowspan="1">
                                <p class="country_id">{{$requirementFinished->country->name}}</p>
                            </td>
                            <td class="description-cell" rowspan="1">
                                <p class="description">{{rtrim($description,';')}}</p>
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
        $('#requirementClosed').addClass('active');
        $("#requirementMangement").addClass("active");
    </script>

@stop
