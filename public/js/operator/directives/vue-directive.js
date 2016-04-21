/**
 * Vue
 * Directives used to operation
 **/

var subStrByByte = function (str, limit) {
	var newStr="";
	var len=0;
	for(var i=0; i<str.length; i++){
		if((/[^\x00-\xff]/g).test(str[i])){
			len +=2;
		}else{
			len +=1;
		}
		if(len>limit){
			newStr=str.substr(0,i);
			return newStr;
		}
	}
	return str;
};

Vue.directive('sub-str', {
	bind: function() {
		var limit = this.expression;
		$(this.el).on('change', function(event) {
			event.preventDefault();
			$(this).val(subStrByByte($(this).val(),limit));
		});
	}
});

Vue.directive('min', {
	bind: function() {
		var that = this;
		$(this.el).on('change', function(event) {
			event.preventDefault();
			if ($(this).val() < that.expression) {
				$(this).val(that.expression);
			}
		});
	}
});

Vue.directive('max', {
	bind: function() {
		var that = this;
		$(this.el).on('change', function(event) {
			event.preventDefault();
			if ($(this).val() > that.expression) {
				$(this).val(that.expression);
			}
		});
	}
});