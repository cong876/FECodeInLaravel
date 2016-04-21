/**
 * controllers for page register
 */

angular.module('hljApp')

	.controller('buyerRegisterCtrl', ['$scope', 'yeyeFn', function($scope, yeyeFn) {

		$scope.getVerifyCode = yeyeFn.getVerifyCode;
		$scope.register = yeyeFn.register;
	}]);
