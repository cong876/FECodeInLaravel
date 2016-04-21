
@extends("operation.partial.master")


@section('content')
@include('operation.partial.informationBasePartial')
  <div id="informationBase" class="tab-content">
    <div class="panel panel-default tab-pane fade in active" style="float: left; width: 50%; max-height: 550px; overflow-y: auto">
      <div class="detail panel-body">
        <form role="form" class="clearfix form-horizontal" action="">
          <div class="col-lg-12">
            <div>
              <label for="">请选择数据来源</label>
            </div>
            <div class="col-lg-8" style="padding: 0">
              <select name="type" class="form-control" v-model="sourceType">
                <option v-for="type in sourceTypes" :value="type.name" v-html="type.name"></option>
              </select>
            </div>
            <div class="col-lg-4" style="padding: 0; text-align: right">
              <button class="btn btn-default" @click.prevent="collect(this)">收集</button>
            </div>
          </div>
          <div class="col-lg-12" style="margin-top: 10px">
            <label for="">请粘贴源数据</label>
            <textarea v-model="source" name="source" rows="10" class="form-control"></textarea>
          </div>
        </form>
      </div>
    </div>
    <div class="panel panel-default tab-pane fade in active" style="float: left; width: 50%; max-height: 550px; overflow-y: auto">
      <div class="detail panel-body">
        <ol class="list-group">
          <li class="list-group-item row" v-for="item in items" style="display: -webkit-box">
            <div style="-webkit-box-flex: 1">
              <span v-html="$index + 1 + '. '"></span><span v-html="item.title || (item.description.subStrByByte(40)) + '...'"></span>
            </div>
          </li>
        </ol>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $("#itemsManagement").addClass("active");
      $("#informationWaitDeal").addClass("active");

      new Vue({
        el: 'body',
        data: {
          sourceTypes: [                                          //可供收集的数据类型
            {name: "小红书"},
            {name: "淘世界"},
            {name: "洋码头笔记"}
          ],
          sourceType: "小红书",                                    //默认选择的数据类型
          source: "",                                             //收集的源数据
          items: []                                               //收集成功的数据展示
        },
        methods: {
          collect: collect
        }
      });

      function collect() {
        var that = this;
        if (that.source.trim() == "") {
          alert("请输入数据");
          return false;
        }
        var data = {
          type: that.sourceType,
          data: (new Function('return ' + that.source))()
        };
        console.log(data);
        $.post("/tips", data, function(res) {
          that.items = res;
          alert("收集成功!")
        })
      }
    });
  </script>

@stop