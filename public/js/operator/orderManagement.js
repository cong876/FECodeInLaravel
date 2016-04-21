        $(document).ready(function () {
            
            var h=document.body.scrollHeight;
            var w=document.body.scrollWidth;
            $("#background").height(h);
            $("#background").width(w);
            window.onresize=function(){
                h=document.body.scrollHeight;
                w=document.body.scrollWidth;
                $("#background").height(h);
                $("#background").width(w);
            };

            $("#order").on("click", ".delete", function (event) {
                event.preventDefault();
                var that = this;
                if(confirm("关闭交易是高危操作，请务必和买家充分沟通过再进行此操作！")){
                    window.location.href="/operator/cancelOrder/"+$(that).parents("tbody").find(".order_id").text();
                }
            })

            $("#order,#remittanceHeader").on("click",".refundRecord",function(event){
                event.preventDefault();
                var that=this;
                if($(that).data("refund-description").indexOf("###")>-1){
                    var time=$(that).data("refund-time").split("###");
                    var title=$(that).data("refund-title").split("###");
                    var number=$(that).data("refund-number").toString().split("###");
                    var price=$(that).data("refund-price").split("###");
                    var description=$(that).data("refund-description").split("###");
                }else{
                    var time=[];
                    var title=[];
                    var number=[];
                    var price=[];
                    var description=[];
                    time.push($(that).data("refund-time"));
                    title.push($(that).data("refund-title"));
                    number.push($(that).data("refund-number"));
                    price.push($(that).data("refund-price"));
                    description.push($(that).data("refund-description"));
                }
                var len=price.length;
                var lenOld=$("#refundRecord").find(".refundDetail").length;
                for(var i=lenOld; i<len; i++){
                    var newDetail=$("#refundRecord").find(".refundDetail").eq(0).clone();
                    $("#refundRecord").find(".justMark").before(newDetail);
                };
                for(var i=lenOld; i>len; i--){
                    $("#refundRecord").find(".refundDetail").eq(i-1).remove();
                };
                for(var i=0; i<len; i++){
                    var index=$("#refundRecord").find(".refundDetail").eq(i);
                    index.find(".refund_time").text(time[i]);
                    index.find(".refund_title").text(title[i]);
                    index.find(".refund_number").text(number[i]);
                    index.find(".refund_price").text(parseFloat(price[i]).toFixed(2));
                    index.find(".refund_description").text(description[i]);
                    if(title[i]==""||title[i]==undefined){
                        $("#refundRecord").find(".refundDetail").eq(i).find("p").eq(1).hide();
                        $("#refundRecord").find(".refundDetail").eq(i).find("p").eq(2).hide();   
                    }else{
                        $("#refundRecord").find(".refundDetail").eq(i).find("p").eq(1).show();
                        $("#refundRecord").find(".refundDetail").eq(i).find("p").eq(2).show();
                    }
                }
                $("#refundRecord").find(".suborder_number").text($(that).parents("tbody").find(".order_id").text());
                $("#refundRecord").fadeToggle("fast");
                $("#background").fadeToggle("fasts");
                if($("#refundRecord").height()>380){
                    $("#refundRecord").height(380)
                }
            })
            
            $("#refundRecord").on("click",".closeRefund",function(event){
                event.preventDefault();
                $("#refundRecord").fadeToggle("fast");
                $("#background").fadeToggle("fasts");              
            })
        })