
(function(window,angular,undefined) {

	'use strict';

	angular.module('yeyeFn',[])
		.provider('yeyeFn', yeyeFnProvider);																										//一些比较通用的函数

	function yeyeFnProvider() {
		this.$get = function($location, $http, $q, $rootScope){																	//将服务注册到provider上
			var that = this;

			function loading() {
				$rootScope.showLoading = true;
			}
			function loaded(init) {
				if (init == 0) {																																		//加载中字样背景不透明，防止用户看到样式错乱的页面，以后要提高，注意html标签的语义化
					angular.element(document.getElementById('loading')).css({backgroundColor: 'rgba(255,255,255,0)'});
				}
				$rootScope.showLoading = false;
			}

			function yeyeReq(needLoading, removeLoading, method, url, data) {											//前两个参数表示请求是否需要显示加载，以及请求完成后是否要隐藏显示的加载字样
				var defer = $q.defer();
				var req = {
					method: method,
					url: url
				};
				if (data) {
					if (method === 'POST' || method === 'PUT') {
						req.data = data;
					} else {
						var para = '?';
						var key;
						for (key in data) {
							para += key + '=' + data[key] + '&';
						}
						para = para.substr(0, para.length - 1);
						req.url += para;
					}
				}

				// if (method === 'POST' || method === 'PUT') {																				//为post请求注入x-csrf-token
					// req.headers = {'X-CSRF-TOKEN': document.querySelector("meta[name=csrf-token]").content};
				// }

				if (needLoading && !$rootScope.showLoading) {																				//当需要展示加载中字样且加载中字样没被显示时，显示加载中字样
					$rootScope.showLoading = true;
				}

				$http(req)
					.then(
						function(res) {																																	//统一的请求成功status处理函数可以放在这里
							if (method === "POST" || method === "PUT") {
								if (res.data.status_code == 200) {
									defer.resolve(res.data);
								} else {
									alert(res.data.message);
									if (removeLoading) {
										$rootScope.showLoading = false
									}
									defer.reject(res.data);
								}
							} else {
								defer.resolve(res.data);
							}
						},
						function(err) {
              alert(url + "," + err);
							defer.reject(err);
						})
					.then(function() {
						if (removeLoading) {
              $rootScope.showLoading = false
            }
					});

				return defer.promise;
			}


			var service = {
				yeyeReq: yeyeReq
			};

			var _user = {
				setUser: function(user) {
					angular.extend(that, {user: user});
				},
				getUser: function() {
					return that.user;
				},
        updateUser: function(data) {
          angular.extend(that.user, data);
        },
				register: function(user, url) {
					yeyeReq(true, true, 'POST', url, user).then(function(res) {
						user.mobile = _deal.trim(user.mobile);
						user.email = _deal.trim(user.email);
						angular.extend(that, {user: user});
						history.back();
					}, function(err) {
						alert(err);
					});
				}
			};

			var _deal = {
				loaded: loaded,
				trim: function(a) {
					if ((typeof a) === "number") {
						a = a.toString();
					}
					return a.split(" ").join("");
				},
				subStrByByte: function(str, limit) {
				  var newStr="";
				  var len=0;
				  for (var i=0; i<str.length; i++) {
				      if ((/[^\x00-\xff]/g).test(str[i])) {
				          len +=2;
				      } else {
				          len +=1;
				      }
				      if (len>limit) {
				          newStr=str.substr(0,i);
				          return newStr + "...";
				      }
				  }
				  return str;
				}
			};

			angular.extend(service, _user, _deal);

			return service;
		}
	}

})(window,window.angular);
