<link rel="stylesheet" href="{{url('css/operator/iris.min.css')}}">

@extends("operation.partial.master")


@section('content')

    <div id="tags" data-tags='<?php echo json_encode($tags)  ?>'></div>

    <div id="tagsList" class="panel panel-default tab-pane fade in active"
         style="width: 50%; position: relative; margin: 0 auto">
        <div class="detail panel-body" style="max-height: 600px; overflow-y: auto;">
            <h4>标签管理 <span class="placeholder">0</span>
                <span>
                    <a class="btn-xs btn-success" href="" role="button" style="padding: 5px"
                       @click="editTag(0, $event)">新建标签</a>
                </span>
            </h4>
            <ol class="list-group">
                <li class="list-group-item row" v-for="tag in tags">
                    <div class="col-sm-2"><span class="badge" :style="tag.style" v-html="tag.tag_name"></span></div>
                    <div class="col-sm-6">标签描述:<span v-html="tag.tag_description"></span></div>
                    <div class="col-sm-4">
                        <a @click="editTag(tag, $event)" class="btn-xs btn-info" role="button">编辑</a>
                        <a v-if="tag.is_available == 1" @click="invalid(tag, $event)" href="" class="btn-xs btn-warning" role="button">失效</a>
                        <a v-else @click="valid(tag, $event)" href="" class="btn-xs btn-success" role="button">恢复</a>
                        <a @click="remove(tag, $event)" href="" class="btn-xs btn-danger" role="button">删除</a>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <div class="modal fade" id="tagEditing" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog" style="width: 600px">
            <div class="modal-content">
                <div class="modal-header" style="text-align: right; padding: 5px 15px">
                    <button @click="save(tag, $event)" type="button" class="btn btn-primary save">保存</button>
                    <span class="placeholder">0</span>
                    <button type="button" class="btn btn-danger closeM" data-dismiss="modal" aria-hidden="true">关闭
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <label>标签预览 <span class="placeholder">0</span></label>
                        <span class="badge" :style="tag.style" v-html="tag.tag_name"></span>
                    </div>
                    <form role="form" method="post" class="clearfix form-horizontal" action="">
                        <label>标签名称</label>
                        <input v-model="tag.tag_name" v-sub-str="10" type="text" class="form-control" placeholder="不超过4个字"/>
                        <label>标签描述</label>
                        <input v-model="tag.tag_description" type="text" class="form-control" placeholder="填写标签描述"/>
                        <label>优先级</label>
                        <select v-model="tag.priority" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                        <div class="col-lg-12 col-md-12" style="padding: 10px 0 0 0">
                            <div class="col-lg-6 col-md-6" style="padding-left:0">
                                <label for="color-picker">标签颜色</label>
                                <input type="text" id='color-picker' v-model="tag.style.color"/>
                            </div>
                            <div class="col-lg-6 col-md-6" style="padding-right:0">
                                <label for="bg-color-picker">背景颜色</label>
                                <input type="text" id='bg-color-picker' v-model="tag.style.backgroundColor"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{url('js/operator/directives/vue-directive.js')}}"></script>
    <script src="{{url('js/operator/iris.min.js')}}"></script>
    <script>

        $("#itemsManagement").addClass("active");

        function sendRequest(url, type, callback) {
            $.ajax({
                url: url,
                type: type,
                success: callback
            });
        }

        $(document).ready(function() {
            var tags = $("#tags").data("tags");

            var tagsList = new Vue({
                el: "#tagsList",
                data: {
                    tags: tags
                },
                methods: {
                    editTag: function(tagIndex, event) {
                        event.preventDefault();
                        tagEditing.tag = {
                            id: tagIndex.id || 0,
                            tag_name: tagIndex.tag_name || "",
                            tag_description: tagIndex.tag_description || "",
                            style: {
                                color: tagIndex == 0 ? "#ffffff" : tagIndex.style.color,
                                backgroundColor: tagIndex == 0 ? "#000000" : tagIndex.style.backgroundColor
                            },
                            priority: 1,
                            is_available: tagIndex == 0 ? 1 : tagIndex.is_available
                        };
                        $('#tagEditing').modal('show');
                        $("#color-picker").iris('color', tagEditing.tag.style.color);
                        $("#bg-color-picker").iris('color', tagEditing.tag.style.backgroundColor);
                    },
                    invalid: function(tag, event) {
                        event.preventDefault();
                        if (confirm("确认使该标签失效?")) {
                            sendRequest('/api/itemTag/' + tag.id + '/invalid', 'put', function(response) {
                                tag.is_available = 0;
                            });
                        } else {
                            return false;
                        }
                    },
                    valid: function(tag, event) {
                        event.preventDefault();
                        if (confirm("确认恢复该标签?")) {
                            sendRequest('/api/itemTag/' + tag.id + '/valid', 'put', function(response) {
                                tag.is_available = 1;
                            });
                        } else {
                            return false;
                        }
                    },
                    remove: function(tag, event) {
                        event.preventDefault();
                        var that = this;
                        if (confirm("确认删除该标签?")) {
                            sendRequest('/api/itemTag/' + tag.id, 'delete', function(response) {
                                that.tags.$remove(tag);
                            });
                        } else {
                            return false;
                        }
                    }
                }
            });

            var tagEditing = new Vue({
                el: "#tagEditing",
                data: {tag: {}},
                methods: {
                    save: function(tag, event) {
                        event.preventDefault();
                        var url,method;
                        if (tag.tag_name == "") {
                            alert('请输入标签名称');
                            return false;
                        }
                        if (tag.id == 0) {
                            delete(tag.id);
                            url = "/api/itemTags";
                            method = "post";
                        } else {
                            url = "/api/itemTag/" + tag.id;
                            method = "put";
                        }
                        console.log(url, method);
                        $.ajax({
                            url: url,
                            type: method,
                            data: tag,
                            success: function (response) {
                                if (response.status_code==200) {
                                    if (tag.id) {
                                        tagsList.tags.map(function(index, i) {
                                          if (index.id == tag.id) {
                                            tagsList.tags.splice(i, 1, tag);
                                          }
                                        });
                                    } else {
                                        tag.id = response.id;
                                        tagsList.tags.push(tag);
                                    }
                                    $('#tagEditing').modal('hide');
                                }
                            }
                        });
                    }
                }
            });

            $('#tagEditing').on('hide.bs.modal', function() {
                $('#color-picker').iris('hide');
                $('#bg-color-picker').iris('hide');
            });


            $('#color-picker').iris({
                palettes: ['#f84c4c', '#ff6774', '#f99247', '#f6ad2d', '#89df6b', '#58d8b5', '#52b4ff', '#f274f7', '#db50d9', '#a04cdb'],
                change: function(event, ui) {
                    tagEditing.tag.style.color = ui.color.toString();
                }
            }).on('focus', function() {
                $('#color-picker').iris('show');
            });

            $('#bg-color-picker').iris({
                palettes: ['#f84c4c', '#ff6774', '#f99247', '#f6ad2d', '#89df6b', '#58d8b5', '#52b4ff', '#f274f7', '#db50d9', '#a04cdb'],
                change: function(event, ui) {
                    tagEditing.tag.style.backgroundColor = ui.color.toString();
                }
            }).on('focus', function() {
                $('#bg-color-picker').iris('show');
            });
        })
    </script>
@stop