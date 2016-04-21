/**
 * controllers for activities
 */

angular.module('hljApp')
  .controller('activityCtrl', ['$scope', 'modalService', 'activityService', '$timeout', '$document', '$location', '$stateParams', 'wxService', 'yeyeFn',
    function ($scope, modalService, activityService, $timeout, $document, $location, $stateParams, wxService, yeyeFn) {

      var targetActivity = $stateParams.id ? $stateParams.id : 'current';
      $scope.carouselPics = [];
      $scope.carouselTargets = [];

      activityService.getActivity(targetActivity, function (dataShow) {

        wxService.onMenuShareTimeline({
          title: dataShow.list.share.title,
          link: location.href + "?title=" + dataShow.list.share.title + "&description=" + dataShow.list.share.description + "&imgUrl=" + dataShow.list.share.pic_url,
          imgUrl: dataShow.list.share.pic_url,
          success: function() {
            toTalkingData({event: "成功转发到朋友圈", area: targetActivity == 'current' ? "今日团购" : "主题性活动", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString()}, user: true, time: true});
            toTalkingData({event: "转发到朋友圈", area: targetActivity == 'current' ? "今日团购" : "主题性活动", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString()}, user: true, time: true});
          },
          cancel: function() {
            toTalkingData({event: "转发到朋友圈", area: targetActivity == 'current' ? "今日团购" : "主题性活动", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString()}, user: true, time: true});
          }
        });
        wxService.onMenuShareAppMessage({
          title: dataShow.list.share.title,
          link: location.href + "?title=" + dataShow.list.share.title + "&description=" + dataShow.list.share.description + "&imgUrl=" + dataShow.list.share.pic_url,
          desc: dataShow.list.share.description,
          imgUrl: dataShow.list.share.pic_url,
          success: function() {
            toTalkingData({event: "成功转发个人或群", area: targetActivity == 'current' ? "今日团购" : "主题性活动", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString()}, user: true, time: true});
            toTalkingData({event: "转发个人或群", area: targetActivity == 'current' ? "今日团购" : "主题性活动", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString()}, user: true, time: true});
          },
          cancel: function() {
            toTalkingData({event: "转发个人或群", area: targetActivity == 'current' ? "今日团购" : "主题性活动", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString()}, user: true, time: true});
          }
        });

        var pics = [];
        var targets = [];

        dataShow.list.themeActivities.map(function (index) {
          pics.push(index.picture);
          targets.push({state: 'activity', id: index.id, title: index.activity_title})
        });

        $scope.carouselPics = pics;
        $scope.carouselTargets = targets;

        $scope.activity = dataShow.list;
				$scope.updateAt = dataShow.updateAt;
        $scope.activityShow = $scope.activity.details;


        $timeout(function () {
          if ($location.hash()) {
            var targetItem = angular.element(document.getElementById($location.hash()));
            $document.scrollToElementAnimated(targetItem);
            $location.hash("").replace();
          } else {
            $document.scrollToElement(angular.element(document.getElementById("activity")));
          }
        });
      });


      $scope.aState = 'today';

      $scope.switch = function (target) {
        $scope.aState = target;
        $scope.activityShow = target == 'today' ? $scope.activity.details : $scope.activity.nextDetails;

        if (target == 'next') {
          toTalkingData({event: "周期团购", area: "点击明日预告", kv: {}})
        }
      };

      $scope.buyNow = function (item, index) {
				if (!yeyeFn.getUser().mobile) {
					$location.path("/buyerRegister");
					return false;
				}
        $scope.itemB = {
          number: item.buy_per_user > 0 ? 1 : 0,
          order_memo: ""
        };
        angular.extend($scope.itemB, item);

        modalService.openOther();
        $scope.buyModal = true;

        toTalkingData({event: targetActivity == 'current' ? "周期团购" : "主题性活动", area: "商品区块-点击立即购买", kv: {activity_title: $scope.activity.activity_title, activity_id: $scope.activity.activity_id.toString(), item_title: item.title, item_id: item.id.toString(), item_index: (index + 1).toString()}, user: true});
      };
      $scope.close = function () {
        modalService.closeOther();
        $scope.buyModal = false;
      };
      $scope.buy = activityService.createOrder;

      $scope.$on("$destroy", function() {
        $scope.close();
      });

      if (targetActivity == 'current') {
        toTalkingData({event: "今日团购PV", area: "", kv: {}, user: true, time: true});
      } else {
        toTalkingData({event: "主题性活动PV", area: "", kv: {}, user: true, time: true});
      }

			$scope.refresh = activityService.refresh;

    }]);
