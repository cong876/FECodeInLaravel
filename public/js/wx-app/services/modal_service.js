'use strict';
/*
 * Modal services
*/

angular.module('hljApp')
	.service('modalService', ['$rootScope', '$compile', function($rootScope, $compile) {

		function forbidden(e) {																							//禁止手机用户滚动页面
			e.preventDefault();
			e.stopPropagation();
		}

		function forbidHandleScroll() {																			//禁止手机用户滚动页面
			document.addEventListener("touchmove",forbidden,false);
		}

		function allowHandleScroll() {																			//解除禁止
			document.removeEventListener("touchmove",forbidden,false);
		}

    var modal = angular.element(document.getElementById("modalContent"));

		$rootScope.hljModal = {
			showModal: false,
			close: function() {
				this.showModal = false;
				allowHandleScroll();
			}
		};

    this.openOther = forbidHandleScroll;                                //调用非通用弹窗时,同样禁用手机用户滚动页面
    this.closeOther = allowHandleScroll;

    this.close = function() {
      $rootScope.hljModal.close();
    };

		this.open = function(template, acceptCallback) {
      forbidHandleScroll();
      modal.html(template);
      $rootScope.hljModal.showModal = true;
      $rootScope.hljModal.accept = acceptCallback;
		}

	}]);

