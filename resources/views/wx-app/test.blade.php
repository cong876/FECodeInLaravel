<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>红领巾小助手</title>
    <!--<link rel="stylesheet" type="text/css" href="http://7xnzm2.com2.z0.glb.qiniucdn.com/main024.min.css">-->
    <link rel="stylesheet" type="text/css" href="{{url('css/wx-app/main025.min.css')}}">
    <script>
      window.onload = function() {
        if (undefined === window.angular) {
          var sourceWithoutCache = document.createElement('script');
          sourceWithoutCache.src = 'http://7xnzm2.com2.z0.glb.qiniucdn.com/main085.min.js?' + + new Date();
          document.body.appendChild(sourceWithoutCache);
        }
      }
    </script>
  </head>
  <body ng-controller="mainCtrl"
        data-init='<?php echo $currentUser ?>'>

    <div id="loading" ng-show="showLoading">
      <div class="loadingContainer">
        <div class="spinner">
          <div class="spinner-container container1">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
            <div class="circle4"></div>
          </div>
          <div class="spinner-container container2">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
            <div class="circle4"></div>
          </div>
          <div class="spinner-container container3">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
            <div class="circle4"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="header">
    </div>

    <div ui-view>
      <span class="icon-user placeholder"></span>
    </div>

    <div class="footer fixbottom" ng-if="$state.is('periodActivity') || $state.is('buypal') || $state.is('buyerAdmin')">
      <nav>
        <ul>
          <li ui-sref="periodActivity" ui-sref-opts="{location: 'replace'}">
            <span ng-class="{'icon-tag': !$state.includes('periodActivity'), 'icon-tag-active': $state.includes('periodActivity')}"></span>
            <p>今日团购</p>
          </li>
          <li ui-sref="buypal" ui-sref-opts="{location: 'replace'}">
            <span ng-class="{'icon-buypal': !$state.includes('buypal'), 'icon-buypal-active': $state.includes('buypal')}"></span>
            <p>帮我代购</p>
          </li>
          <li ui-sref="buyerAdmin" ui-sref-opts="{location: 'replace'}">
            <span ng-class="{'icon-user': !$state.includes('buyerAdmin'), 'icon-user-active': $state.includes('buyerAdmin')}"></span>
            <p>个人中心</p>
          </li>
        </ul>
      </nav>
    </div>

    <div class="modal" ng-show="hljModal.showModal">
      <div class="modalContent">
        <div id="modalContent"></div>
        <div class="btnArea">
          <button ng-click="hljModal.close()">取消</button><button ng-click="hljModal.accept()">确认</button>
        </div>
      </div>
    </div>

    <script>
      var currentHash = location.hash=="" ? "#/buypal" : location.hash;
      currentHash = currentHash.replace(/(%23)|(\?\?)/g, "#");
      history.replaceState("", "", "#/bottomState");
      history.pushState("", "", currentHash);
    </script>

    <script src="http://sdk.talkingdata.com/app/h5/v1?appid=8214B00C6BCEBF3D3535C2B993A1727F&vn=3.0"></script>
    <script>
        var userToTD = JSON.parse(document.body.dataset.init);
        userToTD.hlj_id = userToTD.hlj_id.toString();
        userToTD.mobile = userToTD.mobile.toString();
        delete(userToTD.canKill);
        delete(userToTD.remind);
        delete(userToTD.email);
        delete(userToTD.headImageUrl);
        var userToTDStr = "";
        for (var key in userToTD) {
          userToTDStr += userToTD[key] + ";";
        }
        function toTalkingData(option) {
          var kv = {};
          if (option.user) {
            kv.user = userToTDStr + new Date().toString();
            for (var key in option.kv) {
              kv.user += option.kv[key] + ";"
            }
          }
          if (option.time) {
            kv.occurTime = new Date().toString();
          }
          if (option.kv.item_index) {
            kv.title_index = option.kv.item_index + "-" + option.kv.item_title;
          }
          angular.extend(kv, option.kv);
          TDAPP.onEvent(option.event, option.area, kv);
        }
    </script>
    <!-- wx config -->
    <?php

    $appId  = config('wx.appId');
    $secret = config('wx.appSecret');
    $js = new \Overtrue\Wechat\Js($appId, $secret);
    $js->setUrl(url('singletest/wx?'))

    ?>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?php echo $js->config(array('chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'closeWindow', 'onMenuShareAppMessage','onMenuShareTimeline'), false, true) ?>);
        wx.error(function(res) {
            location.href = location.href.split("?")[0] + "?" + currentHash;
        })
    </script>
    <!-- wx config end -->

    <script type="text/javascript" src="https://one.pingxx.com/lib/pingpp_one.js"></script>
    <!--<script src="{{url('js/wx-app/main085.min.js')}}"></script>-->
    <script src="http://7xnzm2.com2.z0.glb.qiniucdn.com/hljAngular.min.js"></script>
    <script type="text/javascript">
      window.addEventListener("load", function () {
        FastClick.attach(document.body);
      }, false);
    </script>
    <!-- build:js({.tmp,app}) scripts/scripts.js -->
    <script src="{{url('js/wx-app/angular-scroll.min.js')}}"></script>
    <script src="{{url('js/wx-app/modules/yeye_fn.js')}}"></script>
    <script src="{{url('js/wx-app/app.js')}}"></script>
    <script src="{{url('js/wx-app/services/modal_service.js')}}"></script>
    <script src="{{url('js/wx-app/services/buypal_service.js')}}"></script>
    <script src="{{url('js/wx-app/services/data_service.js')}}"></script>
    <script src="{{url('js/wx-app/services/buyer_service.js')}}"></script>
    <script src="{{url('js/wx-app/services/activity_service.js')}}"></script>
    <script src="{{url('js/wx-app/services/wx_service.js')}}"></script>
    <script src="{{url('js/wx-app/directives/directive.js')}}"></script>
    <script src="{{url('js/wx-app/directives/component.js')}}"></script>
    <script src="{{url('js/wx-app/routers.js')}}"></script>
    <script src="{{url('js/wx-app/controllers/main_controller.js')}}"></script>
    <script src="{{url('js/wx-app/controllers/register_controller.js')}}"></script>
    <script src="{{url('js/wx-app/controllers/buypal_controller.js')}}"></script>
    <script src="{{url('js/wx-app/controllers/buyer_controller.js')}}"></script>
    <script src="{{url('js/wx-app/controllers/activity_controller.js')}}"></script>
    <!-- endbuild -->


  </body>
</html>
