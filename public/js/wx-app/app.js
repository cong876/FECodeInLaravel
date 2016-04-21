'use strict';

/**
 * @ngdoc overview
 * @name hljApp
 * @description
 * # hljApp
 *
 * Main module of the application.
 */

var userId = JSON.parse(document.body.dataset.init).hlj_id;

angular
  .module('hljApp', [
    'duScroll',
    'ui.router',
    'ngTouch',
    'yeyeFn'
  ])

  .constant({
    hlj_url: {
      defaultItemPic: 'http://7xnzm2.com2.z0.glb.qiniucdn.com/default.png',
      noItemBg: 'http://7xnzm2.com2.z0.glb.qiniucdn.com/noItemBg.jpg',
      reward: 'http://7xnzm2.com2.z0.glb.qiniucdn.com/reward.jpg',
      operator: 'http://7xnzm2.com2.z0.glb.qiniucdn.com/operator.png',
      callUsCode: 'http://7xnzm2.com2.z0.glb.qiniucdn.com/uscode.jpg',
      noOrderBg: 'http://7xnzm2.com2.z0.glb.qiniucdn.com/noOrderBg.png',

      checkMobile: '/api/buyerRegister/checkMobile',
      getVerifyCode: '/api/getVerificationCode',
      verifyCode: '/api/verifySMSCode',
      updateMobile: '/api/user/' + userId + '/mobileOrEmail',
      updateEmail: '/api/user/' + userId + '/mobileOrEmail',

      buyerRegister: '/api/buyerRegister/updateAccount',
      submitRequirement: '/api/user/' + userId + '/requirements',

      getActivityItems: '/api/buyPal/getGroupItems',
      getRewardItem: '/api/buyPal/getLuckyBagItem',

      getProvinces: '/api/regions/1',
      getSubRegions: '/api/region/',

      getAddresses: '/api/user/' + userId + '/addresses',
      createAddress: '/api/user/' + userId + '/addresses',
      updateAddress: '/api/user/' + userId + '/address/',
      deleteAddress: '/api/user/' + userId + '/address/',

      requirements: '/api/user/' + userId + '/requirements',
      orders: '/api/user/' + userId + '/subOrders',

      deleteRequirement: '/api/user/' + userId + '/requirement/',
      deleteOrder: '/api/user/' + userId + '/subOrder/',
      sureToComplete: '/api/user/' + userId + '/subOrder/',

      currentActivity: '/api/activities/current',
      activity: '/api/activity/',

      createOrder: '/api/user/' + userId + '/subOrders'
    },
    hlj_patt: {
      mobile: /^[0-9]{11}$/,
      email: /^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/
    }
  })

  .value({
    isFirstIn: {
      main: true,
      showReward: false,
      fromBuypal: true
    },
    isCountdown: {
      register: false
    }
  })

  .value('duScrollDuration', 500)

  .run(['$rootScope', '$state', '$stateParams', '$location', 'yeyeFn', 'modalService', 'buypalService', 'wxService', 'isFirstIn', 'isCountdown', 'hlj_url', 'hlj_patt',
    function ($rootScope, $state, $stateParams, $location, yeyeFn, modalService, buypalService, wxService, isFirstIn, isCountdown, hlj_url, hlj_patt) {
      $rootScope.showLoading = true;

      $rootScope.$state = $state;
      $rootScope.$stateParams = $stateParams;

      $rootScope.isCountdown = isCountdown;
      $rootScope.hlj_patt = hlj_patt;
      $rootScope.hlj_url = hlj_url;

      $rootScope.$on('$locationChangeStart', locationChangeStart);
      $rootScope.$on('$locationChangeSuccess', locationChangeSuccess);

      function locationChangeStart(event, newUrl, currentUrl) {
        var newPath = newUrl.split("#")[1];
        var currentPath = currentUrl.split("#")[1];
        var isEmptyRequirement = buypalService.getRequirement().length === 0;

        if (newPath === '/buypal' && currentPath === '/buypal' && !isFirstIn.main) {
          event.preventDefault();
          closeWindow();
          return false;
        }

        isFirstIn.main = false;

        $rootScope.actualLocation = newPath;

        /**
         * 判断从需求列表页回退时,有商品时,是否要清空
         */
        if (newPath === '/buypal' && currentPath === '/buypal/list' && !isEmptyRequirement && !$rootScope.hljModal.showModal) {
          event.preventDefault();
          modalService.open("<p>已添加的商品将被删除，</p><p>确认返回？</p>", function () {
            buypalService.requirement = [];
            history.back();
          });
          return false;
        }

        /**
         * 判断当用户从帮我代购退出浏览器时,是否展示福袋,以及当从非主tab回退时,返回上一级
         */
        if (newUrl.match("bottomState")) {
          event.preventDefault();
          var getCurrentPath = currentPath.split("/");
          if (currentUrl.match('buypal') && yeyeFn.getUser().isNewbuyer && !isFirstIn.showReward) {
            $location.path('/buypal/reward').replace();
            return false;
          }

          var guidePath;
          if (getCurrentPath.length > 3) {
            guidePath = getCurrentPath.slice(0, getCurrentPath.length - 2).join('/');             //退到当前的上一级
            $location.path(guidePath);
            return false;
          } else if (getCurrentPath.length == 3) {
						$state.go('periodActivity');
						return false;
          }
          closeWindow();
        }
      }

      function locationChangeSuccess(event, newUrl, currentUrl) {
        var newPath = newUrl.split("#")[1];
        var currentPath = currentUrl.split("#")[1];

        if ($rootScope.hljModal.showModal) {
          $rootScope.hljModal.showModal = false;
          modalService.closeOther();
        }

        if (newPath === '/buypal' && currentPath === '/buypal/list') {                                //回退成功时清空报价
          buypalService.requirement = [];
        }
      }

      $rootScope.$watch(function () {return $location.path()}, function (newLocation, oldLocation) {
        if($rootScope.actualLocation === newLocation) {                                               //从主页回退关闭窗口
          if (oldLocation == "/periodActivity" || oldLocation == "/buyer" || (oldLocation == "/buypal" && !yeyeFn.getUser().isNewbuyer)) {
            $location.path(oldLocation);
            closeWindow();
          }
        }
      });

      function closeWindow() {
        wxService.close();                                                                            //调用微信接口关闭窗口
      }

    }]);


wx.ready(function(){
  angular.element(document).ready(function () {
    angular.bootstrap(document, ['hljApp']);
  });
});
