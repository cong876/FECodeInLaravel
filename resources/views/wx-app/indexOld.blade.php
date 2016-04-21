<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="<?php echo csrf_token() ?>" />
    <title>帮我代购</title>
    <link rel="stylesheet" type="text/css" href="css/wx-app/main.min.css">
<!--     <link rel="stylesheet" type="text/css"  href="css/wx-app/main.css">
    <link rel="stylesheet" type="text/css" href="css/wx-app/style.css"> -->
  </head>
  <body ng-controller="mainCtrl"
    ng-init='user = <?php echo $currentUser ?>'>

    <div id="loading" ng-show="showLoading">
      <div class="spinner">
      加载中...
      </div>
    </div>

    <div class="header">
    </div>

    <div ui-view>
    </div>

    <div class="footer">
    </div>

    <div class="modal" ng-if="hljModal.showModal">
      <div class="modalContent">
        <p ng-repeat = "content in hljModal.content track by $index" ng-bind="content"></p>
        <div class="btnArea">
          <button ng-click="hljModal.close()">取消</button><button ng-click="hljModal.accept();hljModal.close()">确认</button>
        </div>
      </div>
    </div>

    <!-- wx config -->
    <?php

    $appId  = config('wx.appId');
    $secret = config('wx.appSecret');
    $js = new \Overtrue\Wechat\Js($appId, $secret);

    ?>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?php echo $js->config(array('chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'closeWindow'), false, true) ?>);
    </script>
    <!-- wx config end -->

    <script type="text/javascript" src="http://7xnzm2.com2.z0.glb.qiniucdn.com/hljAngular.min.js"></script>
    <script type="text/javascript">
        window.addEventListener( "load", function() {
            FastClick.attach( document.body );
        }, false );
    </script>
    <script type="text/javascript" src="js/wx-app/scripts.62d6e4b1.js"></script>

    <!--<script src="js/wx-app/modules/yeyeFn.js"></script>
    <script src="js/wx-app/app.js"></script>
    <script src="js/wx-app/services/modalService.js"></script>
    <script src="js/wx-app/services/wxService.js"></script>
    <script src="js/wx-app/routers.js"></script>
    <script src="js/wx-app/controllers/buyPalCtrl.js"></script>
    <script src="js/wx-app/controllers/registerCtrl.js"></script>
    <script src="js/wx-app/services/buyPalService.js"></script> -->

</body>
</html>
