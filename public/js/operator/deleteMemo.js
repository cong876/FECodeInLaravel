	$(document).ready(function(){
		$("#checkRemarks").on("click",".delete",function(event){
			event.preventDefault();
			var that=this;
			var state;
			switch($(".container").children("div").eq(0).attr("id")){
				case  "requireDetail": state=1;
					break;
				case  "divideOrder": state=1;
					break;
				case  "orderDetail": state=2;
					break;
				case  "sellerDetail": state=3;
					break;
				case  "buyerDetail": state=4;
					break;
			};
			console.log({state:state});
			$.ajax({
				url:"/operator/deleteMemo/"+$(that).parents("li").data("id"),
				type:"post",
				dataType:"json",
				data:{
					state: state
				},
		        beforeSend: function (xhr) {
		            var token = $("meta[name=csrf-token]").attr('content');
		            if (token) {
		                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
		            }
		        },
				success:function(response){
					if(response==1){
						$(that).parents("li").remove();
					}
		        },
		        error:function(request,errorType,errorMessage){
		            alert("error:"+errorType+";  message:"+errorMessage);
		        }
		    })
		})
	})