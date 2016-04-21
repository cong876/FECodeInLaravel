/**
 * template here;
 */

angular.module('hljApp')
  .directive('loading', loading)
  .directive('overView', overView)                                                            //overview item
  .directive('activityPreview', ['yeyeFn', '$location', activityPreview])                     //preview activity items
  .directive('logisticsPreview', ['buyerService', logisticsPreview])
	.directive('yeyeTimer', ['$interval', yeyeTimer])
	.directive('secKills', ['$state', 'dataService', 'yeyeFn', 'hlj_url', secKills]);

function loading() {
  return {
    replace: true,
    restrict: 'E',
    template: '<div class="spinner">' +
                '<div class="spinner-container container1">' +
                  '<div class="circle1"></div>' +
                  '<div class="circle2"></div>' +
                  '<div class="circle3"></div>' +
                  '<div class="circle4"></div>' +
                '</div>' +
                '<div class="spinner-container container2">' +
                  '<div class="circle1"></div>' +
                  '<div class="circle2"></div>' +
                  '<div class="circle3"></div>' +
                  '<div class="circle4"></div>' +
                '</div>' +
                '<div class="spinner-container container3">' +
                  '<div class="circle1"></div>' +
                  '<div class="circle2"></div>' +
                  '<div class="circle3"></div>' +
                  '<div class="circle4"></div>' +
                '</div>' +
              '</div>'
  }
}

function overView() {
  return {
    replace: true,
    restrict: 'E',
    template: '<section class="box overView"><div><div>' +
                '<div class="squareImage" yeye-bg="image"></div></div></div>' +
                '<div><p ng-bind="title"></p><p><span>&times;</span><span ng-bind="number"></span>' +
                '<b ng-if="price" class="right" yeye-price="price"></b></p></div>' +
              '</section>',
    scope: {
      image: '@image',
      title: '@title',
      number: '@number',
      price: '@price'
    }
  }
}

function activityPreview(yeyeFn, $location) {
  return {
    replace: true,
    restrict: 'E',
    template: '<div><div class="splitArea">' +
                '<div><hr></div><div>逛一逛</div><div><hr></div>' +
              '</div>' +
              '<div class="activityItemArea">' +
                '<span ng-repeat="item in items">' +
                  '<div class="itemContainer" ng-click="goActivity(item.id)">' +
                    '<div class="itemPictureContainerA"><div class="squareImage" yeye-bg="item.pic_url"></div></div>' +
                    '<p class="itemTitle" ng-bind="item.title"></p>' +
                    '<p><span class="itemPrice" yeye-price="item.price"></span><span class="marketPrice" yeye-price="item.marketPrice"></span></p>' +
                  '</div> ' +
                '</span>' +
                '<div class="itemContainer left_fix"></div>' +
                '<div class="splitArea" ng-click="goActivity()"><div><hr></div><div>点击查看更多</div><div><hr></div></div>' +
              '</div></div>',
    scope: {
      url: '='
    },
    link: function($scope) {
      yeyeFn.yeyeReq(false, false, 'GET', $scope.url)
        .then(function(res) {
          $scope.items = res.data;
        });
      $scope.goActivity = function(itemId) {
        $location.path("/periodActivity").replace().hash("item"+itemId);
      }
    }
  }
}

function logisticsPreview(buyerService){
  return {
    replace: true,
    restrict: 'E',
    template:'<div class="box" ui-sref="buyerAdmin.orders.list.detail.logistics({orderId: orderId})">' +
                '<div><span class="icon-delivered"></span></div>' +
                '<div><div ng-if="!logistics" class="paddingR"><loading></loading></div><p class="obvious" ng-bind="firstLog.context"></p><p ng-bind="firstTime"></p></div>' +
                '<div><span>></span></div>' +
              '</div>',
    scope: {
      orderId: '='
    },
    link: function($scope) {
      buyerService.initLogistics($scope.orderId, function(dataShow) {
        var logistics = dataShow.list;
        $scope.logistics = logistics;
        $scope.firstLog = logistics.track_log[logistics.track_log.length-1];
        $scope.firstTime = $scope.firstLog ? $scope.firstLog.time : '暂无物流信息';
      });
    }
  }
}

function yeyeTimer($interval) {
	return {
		replace: true,
		restrict: 'E',
		template: '<div style="display: inline-block"><span>00</span>:<span>00</span>:<span>00</span></div>',
		scope: {
			serverTime: '=serverTime',
			deadline: '=deadline',
			updateAt: '=updateAt',
			timerEnd: '=timerEnd',
			timerCheck: '=timerCheck'
		},
		link: function($scope, el) {
			var deadline = $scope.deadline;
			var serverTime = $scope.serverTime;
			var updateAt = $scope.updateAt;
			var arrD = deadline.split(/[- :]/);
			var arrS = serverTime.split(/[- :]/);
			var timeD = new Date(arrD[0], arrD[1]-1, arrD[2], arrD[3], arrD[4], arrD[5]);
			var timeS = new Date(arrS[0], arrS[1]-1, arrS[2], arrS[3], arrS[4], arrS[5]);
			var difference = updateAt - timeS;
			var hours = angular.element(el.children()[0]);
			var minutes = angular.element(el.children()[1]);
			var seconds = angular.element(el.children()[2]);

			var countdown = $interval(function() {
				if ($scope.timerCheck) {
					if (timeD - new Date() + difference <= 300000) {
						$scope.timerCheck();
					}
				}
				if (timeD - new Date() + difference <= 0) {
					$scope.timerEnd();
					$interval.cancel(countdown);
					return false;
				}
				var time1 = Math.floor((timeD - new Date() + difference)/3600000);
				var time2 = Math.floor((timeD - new Date() + difference)%3600000/60000);
				var time3 = Math.floor((timeD - new Date() + difference)%3600000%60000/1000);
				hours.text(time1 < 10 ? '0' + time1 : time1);
				minutes.text(time2 < 10 ? '0' + time2 : time2);
				seconds.text(time3 < 10 ? '0' + time3 : time3);
			}, 1000, 0, false);

			$scope.$on("$destroy", function() {
				$interval.cancel(countdown)
			})
		}
	}
}

function secKills($state, dataService, yeyeFn, hlj_url) {
	return {
		replace: true,
		restrict: 'E',
		template:'<div style="background-color:#f5f5f5">' +
								'<div class="box specialItemContainer" ng-repeat="secKill in secKills">' +
									'<div>' +
										'<div class="squareImage" yeye-bg="secKill.item.pic_url"></div>' +
									'</div>' +
									'<div class="specialItem">' +
											'<div><span class="largeSize" ng-bind="secKill.item.title"></span></div>' +
											'<div class="box">' +
												'<div>' +
												'<p class="obvious">￥{{secKill.item.price.substr(0, secKill.item.price.length-1)}}</p>' +
												'<p class="unobvious">国内<span yeye-price="secKill.item.market_price"></span></p>' +
												'</div>' +
												'<div class="btnArea">' +
													'<p class="obvious center" ng-show="!secKill.started">' +
														'<img src="http://7xnzm2.com2.z0.glb.qiniucdn.com/clock.png">' +
														'<yeye-timer ng-show="secKill.willStart" server-time="serverTime" update-at="secKillsOuter.updateAt" deadline="secKill.start_time" timer-end="setStart" timer-check="setWillStart"></yeye-timer>' +
														'<span ng-show="!secKill.willStart">{{secKill.start_time.substr(11,5)}}开抢</span>' +
													'</p>' +
													'<div class="yeye_btn can_get_btn" ng-if="secKill.willStart && !secKill.started">即将开始</div>' +
													'<div class="yeye_btn can_get_btn" ng-if="secKill.started && secKill.is_on_shelf && user.can_sec_kill" ng-click="buy(secKill.item);burying(secKill, 0)">马上抢</div>' +
													'<div class="yeye_btn cannot_get_btn" ng-if="secKill.started && (!secKill.is_on_shelf || !user.can_sec_kill)" ng-click="burying(secKill, 1)">抢光了</div>' +
													'<div ng-show="!secKill.started && !secKill.willStart">' +
														'<div class="yeye_btn black_btn remind_btn" ng-show="!secKill.reminded" ng-click="setRemind(secKill);burying(secKill, 2)">提醒我</div>' +
														'<div class="yeye_btn black_btn cancel_remind_btn" ng-show="secKill.reminded" ng-click="cancelRemind(secKill);burying(secKill, 3)">取消提醒</div>' +
													'</div>' +
												'</div>' +
											'</div>' +
									'</div>' +
								'</div>' +
							'</div>',
		scope: {
			serverTime: '=serverTime',
			user: '=user'
		},
		link: function($scope, el, attrs) {
			function init() {
				if (dataService.data[attrs.state + 'Kills']) {
					dataService.useData(showSecKills, attrs.state + 'Kills', false, true)
				} else {
					dataService.data[attrs.state + 'Kills'] = dataService.newData(attrs.url);
					init();
				}
			}

			function showSecKills(data) {
				data.list.map(function(index) {
					index.willStart = false;
					index.item.pic_url = JSON.parse(index.item.pic_urls)[0];
					index.item.id = index.item.item_id;
				});
				$scope.secKillsOuter = data;
				$scope.secKills = data.list;
			}

			$scope.setStart = function() {
				this.$parent.$apply(function(index) {
					index.secKill.started = true;
				})
			};
			$scope.setWillStart = function() {
				if (!this.$parent.secKill.willStart) {
					this.$parent.$apply(function(index) {
						index.secKill.willStart = true;
					})
				}
			};

			$scope.setRemind = function(secKill) {
				yeyeFn.yeyeReq(false, false, 'POST', '/api/secKill/' + secKill.id + '/user/' + $scope.user.hlj_id + '/remind')
					.then(function(res) {
						secKill.reminded = true;
					});
			};
			$scope.cancelRemind = function(secKill) {
				yeyeFn.yeyeReq(false, false, 'POST', '/api/secKill/' + secKill.id + '/user/' + $scope.user.hlj_id + '/cancelRemind')
					.then(function(res) {
						secKill.reminded = false;
					});
			};

			$scope.buttonClick = new Date();
			$scope.buy = function(item) {
				if (!yeyeFn.getUser().mobile) {
					$location.path("/buyerRegister");
					return false;
				}

				if (new Date() - $scope.buttonClick < 300) {
					return false
				}
				$scope.buttonClick = new Date();

				dataService.data['currentKills'].list.map(function(index) {
					if (index.item.item_id == item.item_id) {
						index.is_on_shelf = false;
						$scope.user.can_sec_kill = false;
					}
				});

				var data = {
					item_id: item.id,
					order_memo: item.order_memo,
					number: item.number
				};
				yeyeFn.yeyeReq(true, true, 'POST', hlj_url.createOrder, data)
					.then(function(res) {
						$state.go('buyerAdmin.orders.list.pay', {state: 'waitPay', id: res.data.id});
					});
			};

			var buryingText = ['秒杀区块-点击马上抢', '秒杀区块-点击抢光啦', '秒杀区块-点击提醒我', '秒杀区块-点击取消提醒'];
			$scope.burying = function(secKill, n) {
				toTalkingData({event: "周期团购", area: buryingText[n], kv: {item_title: secKill.item.title, item_id: secKill.item.item_id.toString(), item_price: secKill.item.price.toString(), start_time: secKill.start_time.toString()}, user: true, time: true})
			};

			init();
		}
	}
}