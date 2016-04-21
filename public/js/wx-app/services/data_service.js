/**
 * store the data from back end, reduce the unnecessary request
 */

angular.module('hljApp')
  .service('dataService', ['$rootScope', '$interval', 'yeyeFn', 'hlj_url',
    function($rootScope, $interval, yeyeFn, hlj_url) {

      function ListData(url, state, state_name) {                                               //the last two params are used to orders
        this.url = url;
        this.list = null;
        this.state = state;
        this.state_name = state_name;
        this.changed = false;
        this.pendding = false;
      }

      ListData.prototype.refresh = function(callback, isProcess) {                              //require callback
        var that = this;
        if (that.pendding) {                                                                    //if data is loading, don't send request
          if (!isProcess) {
            $rootScope.showLoading = true;
          }
          var checkDone = $interval(function(){                                                 //check loading completed or not every 0.1s, largest times is 10
            if (!that.pendding) {
              callback(that);
              $interval.cancel(checkDone);
            }
          }, 200, 10);
          return false;
        } else {
          that.pendding = true;
        }
        yeyeFn.yeyeReq(!isProcess, false, 'GET', that.url)
          .then(function(res) {
            if (res.data) {
              that.list = res.data;
            } else {
              that.list = res;
            }
            if (res.meta) {
              that.meta = res.meta;
            }
            that.updateAt = new Date();
            that.changed = false;
            that.pendding = false;
            callback(that);
          }, function(err) {
            that.pendding = false;
          });
      };


      var YeOrders = {
        waitOffer: new ListData(hlj_url.requirements, 0, "待报价"),
        waitPay: new ListData(hlj_url.orders + "/waitPay", 201, "待付款"),
        waitDelivery: new ListData(hlj_url.orders + "/waitDelivery", 501, "待发货"),       //backend order states:501,521,541
        delivered: new ListData(hlj_url.orders + "/delivered", 601, "已发货"),
        completed: new ListData(hlj_url.orders + "/completed", 301, "已完成")
      };
      var YeProvinces = new ListData(hlj_url.getProvinces);
      var YeAddresses = new ListData(hlj_url.getAddresses);


      this.data = {                                                                             //store data here, can extend
        orders: YeOrders,
        provinces: YeProvinces,
        addresses: YeAddresses,
        subRegions: {},
        logistics: {},
        activities: {}
      };

      var that = this;

			//this.prototype.refreshData = function(){
			//	this.changed = true;
			//};
      this.refreshData = function(data) {                                                       //set data state to need refresh(not refresh immediate), next use refresh
        var dataRefresh = that.data[data] || that.data.orders[data] || that.data.orderDetail[data];
        if (dataRefresh.changed === false) {
          dataRefresh.changed = true;
        } else if (dataRefresh.changed === undefined) {                                         //while data is orderDetail, refresh all orders in orderDetail
          var key;
          for (key in dataRefresh) {
            if (dataRefresh[key].changed === false) {
              dataRefresh[key].changed = true;
            }
          }
        }
      };

      this.newData = function(url, state, state_name) {
        return new ListData(url, state, state_name)
      };

      /**
       * @param  {function}   [callback]
       * @param  {string}     [dataUse]   such as orders, order, addresses or the name has been defined by newData
       * @param  {string}     [dep]       if dataUse is orders, types for orders, or keys for some data
       * @param  {boolean}    [isProcess] weather show loading
       **/
      this.useData = function(callback, dataUse, dep, isProcess) {
        var dataShow = dep ? that.data[dataUse][dep] : that.data[dataUse];
        if (dataShow.list && !dataShow.changed) {
          callback(dataShow);
        } else {
          dataShow.refresh(function() {
            $rootScope.showLoading = false;
            callback(dataShow);
          }, isProcess)
        }
      };

    }]);
