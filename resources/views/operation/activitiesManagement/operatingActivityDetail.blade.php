<link rel="stylesheet" type="text/css" href="{{url('css/activityPreview.css')}}">
@extends("operation.partial.master")


@section('content')

    <div id="tags" data-tags='<?php echo json_encode($tags, JSON_HEX_APOS)?>'></div>
    <div id="items" data-items='<?php echo json_encode($items, JSON_HEX_APOS)?>'></div>
    <div id="killItems" data-items='<?php echo json_encode($killItems, JSON_HEX_APOS)?>'></div>

    <div id="activityDetail">
        <div class="panel panel-default tab-pane fade in active"
             style="float: left; width: 40%; position: relative; margin-bottom: 0">
            <?php if($activity->is_available == false) { ?>
            <a id="saveActivity" class="btn btn-default" role="button"
               style="position: absolute; right: 25px; top: 10px; z-index: 999">保存</a>
            <a id="publishActivity" class="btn btn-default" role="button"
               style="position: absolute; right: 90px; top: 10px; z-index: 999">发布</a>
            <?php } else { ?>
            <a id="saveActivity" class="btn btn-default" role="button"
               style="position: absolute; right: 25px; top: 10px;display:none; z-index: 999">保存</a>
            <a id="saveAndPublishActivity" class="btn btn-default" role="button"
               style="position: absolute; right: 25px; top: 10px; z-index: 999">保存</a>
            <?php } ?>

            <div class="detail panel-body" style="max-height: 600px; overflow-y: auto;">
                <section>
                    <h4>基本信息</h4>
                    <?php
                    $name = App\Models\Employee::find($activity->publisher_id)->real_name;
                    if ($activity->activity_type == 1) {
                        $type = '周期性活动';
                    } elseif ($activity->activity_type == 2) {
                        $type = '主题性活动';
                    }
                    ?>
                    <p>
                        活动ID：<span id="activityId">{{$activity->activity_id}}</span>
                        <span class="placeholder">000</span>
                        活动类型：<span class="activityType" data-type="{{$activity->activity_type}}">{{$type}}</span>
                        <span class="placeholder">000</span>
                        编辑人员：<span>{{$name}}</span>
                    </p>

                    <p>
                        活动日期：<span class="activityStartTime">{{$activity->activity_start_time}}</span> 至
                        <span class="endTime">{{$activity->activity_due_time}}</span>
                        <span class="placeholder">000</span>
                        <a class="btn-xs btn-primary" role="button" style="padding: 5px"
                           data-toggle="modal"
                           data-target="#editTime">编辑</a>
                    </p>
                    <?php if (!empty($activity->activity_title)) {
                        $activity_title = $activity->activity_title;
                    } else {
                        $activity_title = "";
                    }
                    ?>
                    <p>活动标题：<span>{{$activity_title}}</span></p>
                </section>
                <hr>
                <section>
                    <section>
                        <h4>转发</h4>
                        <?php
                        if ($activity->forward_info) {

                            $forward_info = json_decode($activity->forward_info);
                            $forward_title = $forward_info->forward_title;
                            $forward_description = $forward_info->forward_description;
                            $forward_url = $forward_info->forward_pic_url;
                        } else {
                            $forward_title = "";
                            $forward_description = "";
                            $forward_url = "";
                        }
                        ?>
                        <p>转发标题：<input class="share_title" type="text" style="width: 300px" value="{{$forward_title}}">
                        </p>

                        <p>转发摘要：<input class="share_description" type="text" style="width: 300px"
                                       value="{{$forward_description}}"></p>
                    </section>
                    <section>
                        <p>转发小图：</p>

                        <div class="shareImage">
                            @if(($activity->forward_info)&&($forward_info->forward_pic_url))
                                <div class="showImage">
                                    <img src="{{$forward_url}}" class="pic_urls">
                                    <a class="deleteImage" href="">×</a>
                                </div>

                                <div class="addImage" style="display:none">
                                    <input type="file" class="chosefiles" name="shareImage">
                                    <span class="addicon">+</span>
                                </div>
                            @else
                                <div class="showImageEx">
                                    <img src="{{$forward_url}}" class="pic_urls">
                                    <a class="deleteImage" href="">×</a>
                                </div>
                                <div class="addImage">
                                    <input type="file" class="chosefiles" name="shareImage">
                                    <span class="addicon">+</span>
                                </div>
                            @endif
                            <div class="clearfix"></div>
                        </div>
                    </section>
                    <?php if($activity->activity_type == 1){ ?>
                    <section>
                        <p>焦点图：
                            <button role="button" class="btn btn-default" style="position: relative">
                                <input type="file" class="chosefiles" name="carouselImage">
                                <span style="font-weight: 900">+</span>
                            </button>
                        </p>
                        <div class="carouselImage">
                            <?php
                            if($activity->activity_info)
                            {
                            $activity_info = json_decode($activity->activity_info);
                            $activity_url = $activity_info->url;
                            $activity_pic_url = $activity_info->pic_url;
                            ?>
                            @for($i=0;$i<count($activity_pic_url);$i++)
                                <div class="carousel">
                                    <div class="showImage">
                                        <img src="{{$activity_pic_url[$i]}}" class="pic_urls">
                                        <a class="deleteImage" name="deleteCarouselImage" href="">×</a>
                                    </div>
                                    <input type="text" style="width: 300px; margin-top: 82px;"
                                           value="{{$activity_url[$i] ? $activity_url[$i] : ''}}">
                                    <div class="clearfix"></div>
                                </div>
                            @endfor
                            <?php }?>
                        </div>
                    </section>
                    <?php } ?>
                    <section>
                        <p>活动头图：（长宽比2:1,大小不要超过30kb）</p>

                        <div class="activityImage">
                            <?php if(strlen($activity->pic_urls) > 0) { ?>
                            <div class="showImage">
                                <img src="{{$activity->pic_urls}}" class="pic_urls">
                                <a class="deleteImage" href="">×</a>
                            </div>
                            <div class="addImage" style="display:none">
                                <input type="file" class="chosefiles" name="activityImage">
                                <span class="addicon">+</span>
                            </div>
                            <?php } else { ?>
                            <div class="showImageEx">
                                <img src="{{$activity->pic_urls}}" class="pic_urls">
                                <a class="deleteImage" href="">×</a>
                            </div>
                            <div class="addImage">
                                <input type="file" class="chosefiles" name="activityImage">
                                <span class="addicon">+</span>
                            </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                    </section>
                </section>
                <hr>
            </div>
        </div>


        <div class="panel panel-default tab-pane fade in active" style="float: left; width: 60%;">
            <div class="detail panel-body" style="max-height: 600px; overflow-y: auto;">
                <section>
                    <h4>秒杀商品
                        <span>
                            <a class="btn-xs btn-success" role="button" style="padding: 5px"
                               @click.prevent="editItem(newKillItem, killItems.length+1)">新建商品</a>
                        </span>
                    </h4>
                    <section>
                        <ol class="list-group">
                            <li class="list-group-item row" v-for="item in killItems | orderBy 'start_time'" style="display: -webkit-box">
                                <div style="-webkit-box-flex: 1">
                                    <span v-html="item.title"></span>
                                </div>
                                <div>
                                    <a @click.prevent="editItem(item, $index)" class="btn-xs btn-info" role="button">编辑</a>
                                    <a v-if="item.is_available" @click.prevent="invalidItem(item)" href="" class="btn-xs btn-danger" role="button">失效</a>
                                    <a v-else @click.prevent="validItem(item)" href="" class="btn-xs btn-success" role="button">生效</a>
                                    <a v-if="item.is_on_shelf" @click.prevent="putOffItem(item)" href="" class="btn-xs btn-warning" role="button">下架</a>
                                    <a v-else @click.prevent="putOnItem(item)" href="" class="btn-xs btn-success" role="button">上架</a>
                                </div>
                            </li>
                        </ol>
                    </section>
                </section>
                <hr>
                <section>
                    <h4>商品
                        <span>
                            <a class="btn-xs btn-success" role="button" style="padding: 5px"
                               @click.prevent="editItem(newItem, activityItems.length+1)">新建商品</a>
                            <a class="btn-xs btn-primary" role="button" style="padding: 5px"
                               @click.prevent="previewActivity">预览</a>
                        </span>
                    </h4>
                    <section>
                        <ol class="list-group">
                            <li class="list-group-item row" v-for="item in activityItems" style="display: -webkit-box">
                                <div style="-webkit-box-flex: 1">
                                    <input @change="changeOrder(item, item.order)" type="text" v-model="item.order" style="width: 1.5em; border: none">
                                    <span v-html="item.title"></span>
                                    <span v-for="tag in item.tags" class="badge" :style="tag.style" v-html="tag.tag_name"></span>
                                </div>
                                <div>
                                    <a @click.prevent="editItem(item, $index)" class="btn-xs btn-info" role="button">编辑</a>
                                    <a v-if="item.is_on_shelf" @click.prevent="shiftItem(item)" href="" class="btn-xs btn-warning" role="button">下架</a>
                                    <a v-else @click.prevent="unshiftItem(item)" href="" class="btn-xs btn-success" role="button">上架</a>
                                    <a @click.prevent="createShortUrl(item)" href="" class="btn-xs btn-info" role="button">链接</a>
                                </div>
                            </li>
                        </ol>
                    </section>
                </section>
            </div>
        </div>

        <div class="modal fade" id="editTime" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width: 400px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">
                            基本信息：
                        </h4>
                    </div>
                    <?php $update_title_url = '/operator/updateActivityTitle/' . $activity->activity_id; ?>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <form role="form" method="get" class="clearfix" action="{{url($update_title_url)}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <p>活动ID：{{$activity->activity_id}}</p>

                                <p>活动类型：{{$type}}</p>

                                <p>活动标题：{{$activity_title}}</p>
                                <hr>
                                <label class="control-label">活动标题</label>
                                <input type="text" name="activityTitle" class="form-control" value="{{$activity_title}}"
                                       required>
                                <label class="control-label">请选择活动日期</label><br>
                                <input type="Date" name="startTime" id="startTime" class="form-control"
                                       style="display: inline-block; width: 47%" required> 至
                                <input type="Date" name="endTime" id="endTime" class="form-control"
                                       style="display: inline-block; width: 47%" readonly>
                                <hr>
                            </div>
                            <div style="text-align: center">
                                <button type="submit" class="btn btn-default">保存</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $countries = App\Models\Country::all();
        $employees = App\Models\Employee::all();
        ?>
        <div id="itemEditing" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <item-modal
                type="activityItem"
                :item="itemEditing"
                :tags="tags"
                :countries="{{json_encode($countries)}}"
                :employees="{{json_encode($employees)}}"
            >
            </item-modal>
        </div>

        <div id="killItemEditing" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <item-modal
                    type="killItem"
                    :item="itemEditing"
                    :countries="{{json_encode($countries)}}"
                    :employees="{{json_encode($employees)}}"
                    >
            </item-modal>
        </div>

        <div class="modal fade" id="showShortUrl" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog" style="width: 250px">
                <div class="modal-content">
                    <div class="modal-header" style="text-align: right; padding: 5px 15px">
                        <button type="button" class="btn btn-danger closeM" data-dismiss="modal" aria-hidden="true">关闭
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="urlText"></p>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="showQrcode" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog" style="width: 250px">
                <div class="modal-content">
                    <div class="modal-header" style="text-align: right; padding: 5px 15px">
                        <button type="button" class="btn btn-danger closeM" data-dismiss="modal" aria-hidden="true">关闭
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="qrcode"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="{{url('js/swipeSlide.min.js')}}"></script>
    <script src="{{url('js/qrcode.min.js')}}"></script>
    <script src="{{url('js/operator/directives/vue-directive.js')}}"></script>
    <script src="{{url('js/operator/activitiesManagement/operatingActivityDetail.js')}}"></script>


@stop