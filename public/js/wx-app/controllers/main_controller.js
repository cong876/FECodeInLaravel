'use strict';
/**
 * main logic here;
 */

angular.module('hljApp')
	.controller('mainCtrl', ['$scope', 'yeyeFn', '$location', 'wxService', '$rootScope',
		function($scope, yeyeFn, $location, wxService, $rootScope) {
			if(location.hash.match(["#/buypal/reward"])){
				wxService.close();
			}
	    yeyeFn.setUser(JSON.parse(document.body.dataset.init));                       //init user info
	    $scope.stop = function(event) {
	      event.preventDefault();
	      event.stopPropagation();
	      event.stopImmediatePropagation();
	    };

			$rootScope.user = {};
			angular.extend($rootScope.user, yeyeFn.getUser());


			$scope.substr = function(str, limited) {
        var newStr = yeyeFn.subStrByByte(str, limited);
        return newStr[newStr.length-1]==";" ? newStr.substr(0, newStr.length-1) : newStr;
      };

      $scope.doubleNumber = function(n) {
        return n>9 ? n : '0' + n;
      };

			yeyeFn.loaded(0);
		}]);
