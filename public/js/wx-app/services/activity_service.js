/**
 * Service for activity
 */


angular.module('hljApp')
  .service('activityService', ['dataService', 'hlj_url', '$interval', 'yeyeFn', '$state', '$stateParams',
    function(dataService ,hlj_url, $interval, yeyeFn, $state, $stateParams) {

			this.buttonClick = {
				createOrder: new Date()
			};
			var that = this;

      this.getActivity = function(id, callback) {
        if (dataService.data.activities[id]) {
          dataService.useData(callback, 'activities', id);
        } else {
          var url = id == 'current' ? hlj_url.currentActivity : hlj_url.activity + id;
          dataService.data.activities[id] = dataService.newData(url);
          that.getActivity(id, callback);
        }
      };

			this.refresh = function() {
				dataService.refreshData('activities');
				dataService.refreshData('currentKills');
				dataService.refreshData('nextKills');
				$state.transitionTo($state.current, $stateParams, {
					reload: true,
					inherit: false,
					notify: true
				});
			};

      this.createOrder = function(item) {
				if (new Date() - that.buttonClick.createOrder < 1000) {
					return false
				}
				that.buttonClick.createOrder = new Date();

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


    }]);