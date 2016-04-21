/**
 * including order and address
 * maintain the data from back end
 */

angular.module('hljApp')
  .service('buyerService', ['$rootScope', '$location', 'yeyeFn', 'hlj_url', 'dataService', 'modalService', '$state', '$timeout',
    function ($rootScope, $location, yeyeFn, hlj_url, dataService, modalService, $state, $timeout) {

      var that = this;
      var noSelected = {code: 0, name: "--请选择--", zip_code: ""};

      this.getAreaList = function(id) {
        return {
          provinces: [noSelected].concat(dataService.data.provinces.list),
          cities: id > -1 ? [noSelected, dataService.data.addresses.list[id].city] : [noSelected],
          counties: id > -1 ? [noSelected, dataService.data.addresses.list[id].city] : [noSelected]
        }
      };

      this.getAddress = function(id) {
        var addressIndex = {
          id: "",
          receiver_name: "",
          receiver_mobile: "",
          province: noSelected,
          city: noSelected,
          county: noSelected,
          street_address: "",
          zip_code: "",
          is_default: 1
        };
        if (id > -1) angular.extend(addressIndex, dataService.data.addresses.list[id]);
        return addressIndex;
      };

      this.getSubRegions = function(region, callback) {
        if (dataService.data.subRegions[region.code]) {
          dataService.useData(callback, "subRegions", region.code);
        } else {
          dataService.data.subRegions[region.code] = dataService.newData(hlj_url.getSubRegions + region.code + '/subRegions');
          that.getSubRegions(region, callback);
        }
      };

      this.toManagementAddress = function() {
        $location.url('/buyer/addressManagement?fromChoose=true').replace();
      };

      this.createAddress = function() {
        $location.path('/buyer/addressDetail/-1');
      };

      this.editAddress = function(index) {
        $location.path('/buyer/addressDetail/' + index);
      };

      this.saveAddress = function(address, index) {
        var url, method;
        if (index > -1) {
          url = hlj_url.updateAddress + address.id;
          method = "PUT";
        } else {
          url = hlj_url.createAddress;
          method = "POST"
        }

        var data = {};
        angular.extend(data, address);
        data.province = address.province.code;
        data.city = address.city.code;
        data.county = address.county.code;

        yeyeFn.yeyeReq(true, true, method, url, data)
          .then(function (res) {
            if (address.is_default == 1) {
              angular.forEach(dataService.data.addresses.list, function (item, i) {
                if (item.is_default == 1 && i!=index) item.is_default = 0;                              //set old default to not default
              })
            } else if (index > -1 && dataService.data.addresses.list[index].is_default == 1) {          //while update a default address to not default, must have a default address
              address.is_default = 1;
            }

            if (index > -1) {
              dataService.data.addresses.list[index] = address;
            } else {
              address.id = res.id;
              dataService.data.addresses.list.push(address);
            }

            if (that.chose.address && that.chose.address.id == address.id) {
              that.chose.address = address;
            }

            if ($location.search().fromChoose) {
              that.chooseAddress(index);
            }

            history.back();
          });
      };

      this.deleteAddress = function(address, id) {
        modalService.open('<p>确认删除该地址？</p>', function() {
          yeyeFn.yeyeReq(true, true, 'DELETE', hlj_url.deleteAddress + address.id)
            .then(function (res) {
              if (res.status_code == 200) {
                dataService.data.addresses.list.splice(id, 1);
                if (address.is_default == 1 && dataService.data.addresses.list.length>0) {
                  dataService.data.addresses.list[0].is_default = 1;
                }

                if (dataService.data.addresses.list.length == 0) {
                  that.chose.address = undefined;
                }
                modalService.close();
              } else {
                alert(res.message);
              }
            })
        });
      };

      this.chose = {};                                                                                  //used to choose address

      this.setDefaultAddressChose = function() {
        angular.forEach(dataService.data.addresses.list, function (address) {
          if (address.is_default == 1) {
            that.chose.address = address;
            return that.chose;
          }
        });
        return that.chose;
      };

      this.setAddressChose = function() {
        return that.chose.address ? that.chose.address : that.setDefaultAddressChose();
      };

      this.toChooseAddress = function() {
        if (!dataService.data.addresses.list || dataService.data.addresses.list.length === 0) {
          $location.path('/buyer/addressDetail/-1');
        } else {
          $location.path('/buyer/addressChoose');
        }
      };

      this.chooseAddress = function(index) {
        $timeout(function() {
          history.back();
        });
        that.chose.address = dataService.data.addresses.list[index == -1 ? dataService.data.addresses.list.length - 1 : index];
      };

      this.getOrder = function(id) {
        var orderState = $location.path().split('/')[3];
        var i;
        for (i = 0; i < dataService.data.orders[orderState].list.length; i++) {
          if (id == dataService.data.orders[orderState].list[i].id) {
            return dataService.data.orders[orderState].list[i];
          }
        }
      };

      this.loadMoreOrder = function(meta, $scope) {
        $scope.loadingMore = true;
        yeyeFn.yeyeReq(false, false, 'GET', meta.pagination.links.next)
          .then(function(res) {
            var orderState = $location.path().split('/')[3];
            dataService.data.orders[orderState].list = dataService.data.orders[orderState].list.concat(res.data);
            dataService.data.orders[orderState].meta = res.meta;
            $scope.loadingMore = false;
          })
      };

      function removeOrderNeedBack() {
        if ($location.path().split("/").length > 4) {
          history.back();
        }
      }

      this.offset = 0;                                                                                  //used to order list scroll while back from order detail

      this.checkOrder = function(id) {
        $state.go('buyerAdmin.orders.list.detail', {id: id});
        that.offset = document.body.scrollTop;
      };

      this.payNow = function(id, replace) {
        if (replace) {
          $state.go('buyerAdmin.orders.list.pay', {id: id}, {location: 'replace'});
        } else {
          $state.go('buyerAdmin.orders.list.pay', {id: id});
        }
        if (that.offset == 0) {                                                                         //if from detail to pay, do not set offset
          that.offset = document.body.scrollTop;
        }
      };

      this.sureToPay = function(order) {
        pingpp_one.init({
          app_id: 'app_WzDmrT0CC8G0HSen',
          order_no: order.order_number,
          amount: parseFloat(order.price)*100,
          channel: ['wx_pub'],
          charge_url: "/payment/requestPay",
          charge_param:{
            title: order.title.replace(/[\n\r]/,''),
            receiving_address_id: that.chose.address.id,
            user_id: userId
          },
          open_id:'olxLuv7ftcxC48-YGe6go_E-0FMo'
        },function(res){
          if(!res.status){
            alert(res.msg);
          }else{
            angular.forEach(dataService.data.orders['waitPay'].list, function (index, i) {
              if (index.id == order.id) {
                dataService.data.orders['waitPay'].list.splice(i, 1);
                dataService.refreshData('waitDelivery');                                                //refresh next tab
                return false;
              }
            });
            $state.go("buyerAdmin.orders.list.pay.paySuccess", {id: order.id}, {location: 'replace'});
          }
        });
      };

      function removeOrderFromList(id, method, url, word, refresh) {
        modalService.open("<p>" + word + "</p>", function () {
          var orderState = $location.path().split('/')[3];                                              //get state from url
          yeyeFn.yeyeReq(true, true, method, url)
            .then(function (res) {
              if (res.status_code == 200) {
                modalService.close();
                angular.forEach(dataService.data.orders[orderState].list, function (index, i) {
                  if (index.id == id) {
                    dataService.data.orders[orderState].list.splice(i, 1);
                    if (refresh) dataService.refreshData(refresh);                                      //refresh next tab
                    return false;
                  }
                });
                removeOrderNeedBack();
              } else {
                alert(res.message);
              }
            });
        });
      }

      this.deleteOrder = function(id) {
        var orderState = $location.path().split('/')[3];
        var url = orderState === "waitOffer" ? hlj_url.deleteRequirement + id : hlj_url.deleteOrder + id;
        removeOrderFromList(id, 'DELETE', url, "是否取消该订单？");
      };

      this.hideOrder = function(id) {
        removeOrderFromList(id, 'DELETE', hlj_url.deleteOrder + id + '/hide', "是否删除该订单？");
      };

      this.sureToComplete = function(id) {
        removeOrderFromList(id, 'POST', hlj_url.sureToComplete + id + '/received', "确认已收货？", 'completed');
      };

      this.checkRefund = function(refund) {
        var refundHtml = '<div class="refundDetail">';
        refund.map(function(index) {
          refundHtml += "<p>退款金额： ￥" + (parseFloat(index.amount)/100).toFixed(2) + "</p><p>退款说明：" + index.description + "</p><hr/>";
        });
        refundHtml += "</div><p>退款通常5个工作日内到账</p>";
        modalService.open(refundHtml, function() {
          modalService.close();
        })
      };

      this.callOperator = function(operatorMobile) {
        modalService.open("<p>拨打客服电话：" + operatorMobile + "</p><p>（客服工作时间：9:00-18:00）</p>", function() {
          window.location.href = "tel:" + operatorMobile;
        })
      };

      this.updateMobile = function(mobile, callback) {
        yeyeFn.yeyeReq(true, true, 'PUT', hlj_url.updateMobile, {mobile: mobile})
          .then(function (res) {
            yeyeFn.updateUser({mobile: mobile});
            $rootScope.user.mobile = mobile;
            callback('mobileEditM');
          })
      };

      this.updateEmail = function(email, callback) {
        if (email != $rootScope.user.email) {                                                           //no changes, do not send request
          yeyeFn.yeyeReq(true, true, 'PUT', hlj_url.updateEmail, {email: email})
            .then(function (res) {
              yeyeFn.updateUser({email: email});
              $rootScope.user.email = email;
              callback('emailEditM');
            })
        } else {
          callback('emailEditM');
        }
      };

      this.initLogistics = function(orderId, callback) {
        if (dataService.data.logistics[orderId]) {
          dataService.useData(callback, 'logistics', orderId, true);
        } else {
          dataService.data.logistics[orderId] = dataService.newData('/api/subOrder/' + orderId + '/deliveryInfo');
          that.initLogistics(orderId, callback);
        }
      }
    }]);
