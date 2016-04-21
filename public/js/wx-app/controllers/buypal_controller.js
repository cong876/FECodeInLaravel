'use strict';
/**
 * Controllers for buypal
 * Including the main controller buypalCtrl and child controller itemDetailCtrl,preventCloseCtrl,rewardCtrl,submittedCtrl
 */


angular.module('hljApp')
  .controller('buypalCtrl', ['$scope', 'buypalService', function($scope, buypalService) {

    $scope.requirement = buypalService.getRequirement();

    toTalkingData({event: "帮我代购PV", area: "", kv: {}, user: true, time: true})
  }])

  .controller('itemListCtrl', ['$scope', 'buypalService', '$timeout',
    function($scope, buypalService, $timeout) {

      function calculateTotal() {
        $scope.totalNumber = $scope.requirement.length>0 ? $scope.requirement.map(function(x) {
          return x.number;
        }).reduce(function(a,b) {
          return a+b;
        }) : 0;
      }

      $scope.requirement = buypalService.getRequirement();

      calculateTotal();

      $scope.deleteItem = function(id) {
        buypalService.deleteItem(id);
        calculateTotal();
      };
      $scope.editItem = buypalService.editItem;
      $scope.submitRequirement = buypalService.submitRequirement;

      $timeout(function() {
        window.scrollTo(0, document.body.clientHeight);																						//滑到页面底部使添加按钮显示出来
      },0);

    }])

	.controller('itemDetailCtrl', ['$scope', '$stateParams', 'buypalService',
		function($scope, $stateParams, buypalService) {

      $scope.item = buypalService.getItem($stateParams.itemId);

      $scope.addPicture = function(event, item){
        buypalService.addPicture(event, item, $scope);
      };
      $scope.previewPicture = buypalService.previewPicture;
      $scope.deletePicture = buypalService.deletePicture;
      $scope.saveItem = buypalService.saveItem;

      if ($stateParams.itemId == -2) {
        toTalkingData({event: "帮我代购", area: "空列表区块-点击添加", kv: {}, time: true, user: true});
      } else if ($stateParams.itemId == -1) {
        toTalkingData({event: "帮我代购", area: "列表区块-点击添加", kv: {}, time: true, user: true});
      }
	}])

	.controller('rewardCtrl', ['$scope', 'buypalService',
		function($scope, buypalService) {

      buypalService.initReward($scope);

      $scope.getReward = function() {
        buypalService.getReward($scope);
      };
      $scope.buyReward = buypalService.buyReward;

	}])

	.controller('submittedCtrl', ['$scope',function($scope) {

  }]);
