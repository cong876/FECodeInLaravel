/**
 * Controllers for user admin and it's children
 * Including
 */

angular.module('hljApp')
  .controller('buyerCtrl', ['$scope', 'yeyeFn', 'dataService',
    function ($scope, yeyeFn, dataService) {

      $scope.orderNumber = {waitPayNumber: 0};

      dataService.refreshData('waitOffer');
      dataService.refreshData('waitPay');

      dataService.data.addresses.refresh(function () {
      }, true);                  //pre load the addresses list

      dataService.data.orders.waitPay.refresh(function (that) {                  //pre load the wait pay orders
        $scope.orderNumber.waitPayNumber = that.list.length;
      }, true);

    }])

  .controller('buyerProfileCtrl', ['$scope', 'buyerService', 'modalService',
    function ($scope, buyerService, modalService) {

      $scope.showEdit = function (type, info) {
        modalService.openOther();
        $scope[type] = info;
        $scope[type + 'M'] = true;
      };

      $scope.close = function (type) {
        modalService.closeOther();
        $scope[type] = false;
      };

      $scope.updateMobile = buyerService.updateMobile;
      $scope.updateEmail = buyerService.updateEmail;

      $scope.$on("$destroy", $scope.close);
    }])

  .controller('addressChooseCtrl', ['$scope', 'buyerService', 'dataService',
    function ($scope, buyerService, dataService) {

      $scope.state = "choose";
      $scope.dealer = "管理收货地址";
      $scope.chose = buyerService.chose;

      $scope.checkAddress = buyerService.chooseAddress;
      $scope.dealAddress = buyerService.toManagementAddress;

      dataService.useData(function (dataShow) {
        $scope.addresses = dataShow;                                            //use array in object to ensure two way data binding
      }, "addresses");

    }])

  .controller('addressManagementCtrl', ['$scope', 'buyerService', 'dataService',
    function ($scope, buyerService, dataService) {

      $scope.state = "management";
      $scope.dealer = "添加新地址";

      $scope.checkAddress = buyerService.editAddress;
      $scope.dealAddress = buyerService.createAddress;
      $scope.deleteAddress = buyerService.deleteAddress;

      dataService.useData(function (dataShow) {
        $scope.addresses = dataShow;
      }, "addresses");

    }])

  .controller('addressDetailCtrl', ['$scope', '$timeout', 'buyerService', '$stateParams', 'dataService',
    function ($scope, $timeout, buyerService, $stateParams, dataService) {

      var noSelected = {code: 0, name: "--请选择--", zip_code: ""};
      var isEdit = {cityReady: false, countyReady: false, zip_codeReady: false};

      dataService.useData(function () {
        $scope.hasNoAddress = !dataService.data.addresses.list.length;
      }, "addresses");

      dataService.useData(function () {
        $scope.areaList = buyerService.getAreaList($stateParams.addressId);
      }, "provinces");

      $scope.address = buyerService.getAddress($stateParams.addressId);


      $scope.changeDefault = function () {
        $scope.address.is_default == 0 ? $scope.address.is_default = 1 : $scope.address.is_default = 0;
      };

      $scope.checkForm = function () {
        $scope.form.check = true;
        $timeout(function () {
          if ($scope.form) $scope.form.check = false;
        }, 1500)
      };

      $scope.saveAddress = buyerService.saveAddress;


      $scope.$watch('address.province', function (p) {                           //cascading drop-down cities
        if (isEdit.cityReady) {                                                 //while edit old address, set a noSelected as the first option
          $scope.areaList.cities = [noSelected];
          $scope.areaList.counties = [noSelected];
          $scope.address.city = noSelected;
          $scope.address.county = noSelected;
        }
        if (p.code === 0) {
          return false;
        }
        buyerService.getSubRegions(p, function (res) {
          $scope.areaList.cities = [noSelected].concat(res.list);               //concat the other subRegions to the options
          if (!isEdit.cityReady) isEdit.cityReady = true;
        })
      });

      $scope.$watch('address.city', function (c) {                               //cascading drop-down counties
        if (isEdit.countyReady) {
          $scope.areaList.counties = [noSelected];
          $scope.address.county = noSelected;
        }
        if (c.code === 0) {
          return false;
        }
        buyerService.getSubRegions(c, function (res) {
          $scope.areaList.counties = [noSelected].concat(res.list);
          if (!isEdit.countyReady) isEdit.countyReady = true;
        })
      });

      $scope.$watch('address.county', function (c) {
        if (isEdit.zip_codeReady) {                                             //while edit old address, get zip_code from address rather than county.zip_code
          $scope.address.zip_code = c.zip_code;
        } else {
          isEdit.zip_codeReady = true;
        }
      })

    }])

  .controller('orderCtrl', ['$scope', 'buyerService',
    function ($scope, buyerService) {

      $scope.deleteOrder = buyerService.deleteOrder;
      $scope.callOperator = buyerService.callOperator;
      $scope.sureToComplete = buyerService.sureToComplete;
      $scope.hideOrder = buyerService.hideOrder;
      $scope.checkRefund = buyerService.checkRefund;

    }])

  .controller('orderListCtrl', ['$scope', 'buyerService', 'dataService', '$location', '$stateParams', '$timeout', '$anchorScroll',
    function ($scope, buyerService, dataService, $location, $stateParams, $timeout, $anchorScroll) {

      if ($stateParams.state === 'waitPay') {
        $scope.$watchCollection('orders.list', function (n) {
          if (n) $scope.orderNumber.waitPayNumber = n.length;
        });
      }

      $location.hash("buyerAdmin");

      dataService.useData(function (dataShow) {
        $scope.orders = dataShow;
        $timeout(function () {
          $anchorScroll();
          buyerService.offset = 0;
        });
      }, "orders", $stateParams.state);

      $scope.checkOrder = buyerService.checkOrder;
      $scope.payNow = buyerService.payNow;

      $scope.loadingMore = false;
      $scope.loadMoreOrder = function (meta) {
        buyerService.loadMoreOrder(meta, $scope);
      };

      $scope.$on("$destroy", function () {
        $anchorScroll.yOffset = 0;
      })
    }])

  .controller('orderDetailCtrl', ['$scope', '$stateParams', 'buyerService', 'dataService', '$document', '$anchorScroll',
    function ($scope, $stateParams, buyerService, dataService, $document, $anchorScroll) {

      $scope.$on("$destroy", function () {
        $anchorScroll.yOffset = -buyerService.offset;
      });

      $anchorScroll.yOffset = 0;
      $anchorScroll();

      $scope.sureToPay = buyerService.sureToPay;

      dataService.useData(function (dataShow) {                                  //this page has depend on the data orders
        $scope.orders = dataShow;
        $scope.order = buyerService.getOrder($stateParams.id);
      }, "orders", $stateParams.state);

      dataService.useData(function () {
        buyerService.setAddressChose();
        $scope.chose = buyerService.chose;                                      //used to choose address in page ensure to pay
        $scope.toChooseAddress = buyerService.toChooseAddress;
      }, "addresses");

    }])
  .controller('paySuccessCtrl', ['$scope', '$state',
    function ($scope, $state) {
      $scope.day = 7;
      if ($scope.order.country == '美国') {
        $scope.day = 10;
      }

      $scope.back = function () {
        $state.go('buyerAdmin', {}, {location: 'replace'});
      }
    }])

  .controller('orderItemDetailCtrl', ['$scope', '$stateParams', 'wxService',
    function ($scope, $stateParams, wxService) {

      $scope.items = $scope.order.details;

      $scope.index = $stateParams.index;
      $scope.previewImage = wxService.previewImage;

    }])
  .controller('logisticsCtrl', ['$scope', 'buyerService', '$stateParams',
    function ($scope, buyerService, $stateParams) {
      var orderId = $stateParams.orderId;

      buyerService.initLogistics(orderId, function (dataShow) {
        $scope.logistics = dataShow.list;
      });

    }]);
