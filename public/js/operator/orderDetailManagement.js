
        $(document).ready(function(){

            $("#orderMangement").addClass("active");
            var h=document.body.scrollHeight;
            var w=document.body.scrollWidth;
            if(h<$("#editRequireDetail").height()){
                h=$("#editRequireDetail").height()+60;
            };
            $("#background").height(h);
            $("#background").width(w);
            window.onresize=function(){
                h=document.body.scrollHeight;
                w=document.body.scrollWidth;
                if(h<$("#editRequireDetail").height()){
                    h=$("#editRequireDetail").height()+60;
                };
                $("#background").height(h);
                $("#background").width(w);
            };

            var editIndex=0;

            $("#orderDetail").on("click",".changeBuyer",function(event){               //修改买手
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#changeBuyer").slideToggle("fast");
            })

            $("#buyer_country").on("change",function(event){                          //改国家加载买手u
                event.preventDefault();
                event.stopImmediatePropagation();
                var that=this;
                $.ajax({
                    url:"/operator/getBuyer/"+$(that).val(),                            //请求买手的地址
                    type:"get",
                    dataType:"json",
                    beforeSend: function (xhr) {
                        var token = $("meta[name=csrf-token]").attr('content');
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        }
                    },
                    success: function (response){
                        $("#buyer").html("<option value='0'>--请选择--</option>");
                        for(var i=0; i<response[0].length; i++){
                            var newSeller=$("<option></option>");
                            newSeller.attr({"value":response[0][i],"data-pingyin":response[2][i],"data-abbreviation":response[3][i]});
                            newSeller.text(response[1][i]);
                            $("#buyer").append(newSeller);
                        }
                    },
                    error: function (request,errorType,errorMessage){
                        alert("error:"+errorType+";  message:"+errorMessage);
                    }
                })
            })

            $("#changeBuyer").on("click",".submit",function(event){
                event.preventDefault();
                event.stopImmediatePropagation();
                if(($("#buyer_country").val()!=0)&&($("#buyer").val()!=0)&&($("#reason").val()!="")){
                    $("#changeBuyer").find("form").submit();
                }else{
                    alert("请先分配买手并填写更换原因");
                    return false;
                };
            })

            $("#changeBuyer").on("click",".closed",function(event){                   //关闭选择
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#changeBuyer").slideToggle("fast");
            })

            $("#orderDetail").on("click",".toReceive",function(event){                //运营替买家确认收货
                event.preventDefault();
                if(confirm("请与买家联系后，确认买家收到货物后再进行此操作。")){
                    window.location.href="/operator/commitToHasFinished/"+$("#orderDetail").find(".requirement_id").text();
                }else{
                    return false;
                }
            })

            $("#orderDetail").on("click",".refundRecord",function(event){                      //查看退款记录页
                event.preventDefault();
                var that=this;
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

            $("#orderDetail").on("click",".pass",function(event){                    //通过审核
                event.preventDefault();
                if(confirm("请确认买手已经发货，再进行操作。")){
                    window.location="/operator/commitToHasDelivered/"+$("#orderDetail").find(".requirement_id").text();
                }
            })

            $("#orderDetail").on("click",".unPass",function(event){                  //不通过审核
                event.preventDefault();
                if(confirm("请确认买手没有发货，在进行操作")){
                    window.location="/operator/commitToUndelivered/"+$("#orderDetail").find(".requirement_id").text();
                }
            })

            $("#orderDetail").on("click",".sendInfo",function(event){                //填写快递单
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#sendInfo").slideToggle("fast");
            })

            $("#express").on("change",function(event){
                event.preventDefault();
                var that=this;
                if($(that).val()=="otherCompany"){
                    $("#otherExpress").show();
                    $("#otherExpress").attr({"required":"required"});
                    $("#pinyin").val("");
                }else{
                    $("#otherExpress").hide();
                    $("#otherExpress").removeAttr("required");
                    $("#pinyin").val($("#express").find("option").eq($("#express").val()).data("pinyin"))
                }
            })

            $("#sendInfo").on("click",".cancle",function(event){                        //关闭快递单填写
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#sendInfo").slideToggle("fast");
            })

            $("#orderDetail").on("click",".refund",function(event){                      //退全款
                event.preventDefault();
                $("#refund").find(".refundOrderId").val($(".requirement_id").text());
                $("#background").fadeToggle("fast");
                $("#refund").fadeToggle("fast");
            })

            $("#orderDetail").on("click",".refundPart",function(event){                //退部分款
                event.preventDefault();
                $("#refundPart").find(".refundOrderId").val($(".requirement_id").text());
                $("#refundPart").find(".refundItemId").val($(this).parents("tbody").data("item-id"));
                $("#refundPart").find(".refundItemTitle").val($(this).parents("tbody").find(".title").text());
                $("#refundPart").find(".refundAmount").val("");
                $("#refundPart").find(".refundAmount").data("price",parseFloat($(this).parents("tbody").find(".price").text()).toFixed(2));
                $("#refundPart").find(".refundItemNumber").val("");
                $("#refundPart").find(".refundItemNumber").attr({"max":parseFloat($(this).parents("tbody").data("inventory")).toFixed(2)});
                $("#refundPart").find(".refundDescription").val("");
                $("#background").fadeToggle("fast");
                $("#refundPart").fadeToggle("fast");              
            })

            $("#refundPart").on("input",".refundItemNumber",function(event){
                event.preventDefault();
                $("#refundPart").find(".refundAmount").val(($("#refundPart").find(".refundAmount").data("price")*$(this).val()).toFixed(2));
            })

            $("#refund,#refundPart").on("click",".cancle",function(event){
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $(this).parents("form").parent("div").fadeToggle("fast");
            })

            $("#orderDetail").on("click",".checkOut",function(event){                  //查看商品详情
                event.preventDefault();
                editIndex=$(this).parents("tbody").prevAll("tbody").length;
                var requirementIndex=$(this).parents(".requirement");
                $("#editRequireDetail").find(".itemTitle").val(requirementIndex.data("item-title"));
                $("#editRequireDetail").find(".price").val($.trim(requirementIndex.find(".price").text()));
                $("#editRequireDetail").find(".number").val(requirementIndex.find(".number").text());
                $("#editRequireDetail").find(".description").val(requirementIndex.find(".description").text());
                $("#editRequireDetail").find(".operatingNotes").val(requirementIndex.find(".operatingNotes").data("opnote-id"))
                var imgIndex=requirementIndex.find(".imageContainer").data("img");
                if(!requirementIndex.find(".imageContainer").attr("data-img")==""){
                    for(var i=0; i<imgIndex.length; i++){
                        var showImage=$("#editRequireDetail").find(".showImageEx").clone().attr({"class":"showImage"});
                        showImage.children("img").attr({"src":imgIndex[i]});
                        $("#editRequireDetail").find(".addImage").before(showImage);
                    };
                }else{
                    var showImage=$("#editRequireDetail").find(".showImageEx").clone().attr({"class":"showImage"});
                    showImage.children("img").attr({"src":$(this).parents("tbody").find(".thumb_url").attr("src")});
                    $("#editRequireDetail").find(".addImage").before(showImage);
                }
                $("#background").fadeToggle("fast");
                $("#editRequireDetail").fadeToggle("fast");
            })

            $("#editRequireDetail").on("click","img",function(event){         //显示图片轮播
                event.preventDefault();
                event.stopImmediatePropagation();
                var that=this;
                var imageIndex=$(that).parents(".imageArea").children(".showImage");
                var imageNumber=imageIndex.length;
                for(var i=0; i<imageNumber; i++){
                    if(i==0){
                        $("#myCarousel").find(".item").children("img").attr({"src":imageIndex.eq(0).children("img").attr("src")})
                    }else{
                        var olIndex=$("#myCarousel").children(".carousel-indicators");
                        var addLi=olIndex.children("li").first().clone();
                        addLi.attr({"data-slide-to":i});
                        olIndex.append(addLi);
                        var divIndex=$("#myCarousel").children(".carousel-inner");
                        var addDiv=divIndex.children("div").first().clone();
                        addDiv.children("img").attr({"src":imageIndex.eq(i).children("img").attr("src")});
                        divIndex.append(addDiv);
                    }
                }
                $("#myCarousel").find("li").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").find(".item").eq($(that).parents(".showImage").prevAll(".showImage").length).addClass("active");
                $("#myCarousel").show();
            })

            $("#background").on("click",function(event){                       //关闭图片轮播
                event.preventDefault();
                var len=$("#myCarousel").find("li").length;
                $("#myCarousel").hide();
                setTimeout(function(){
                    for(var i=0;i<len; i++){
                        if(i==0){
                            $("#myCarousel").find("li").eq(0).attr({"class":""});
                            $("#myCarousel").find(".item").eq(0).attr({"class":"item"});
                        }else{
                            $("#myCarousel").find("li").eq(1).remove();
                            $("#myCarousel").find(".item").eq(1).remove();
                        }
                    }
                },500)
            })

            $("#editRequireDetail").on("click",".closed",function(event){               //关闭编辑
                event.preventDefault();
                $("#background").fadeToggle("fast");
                $("#editRequireDetail").fadeToggle("fast");
                var imageNumber=$("#editRequireDetail").find(".showImage").length;
                for(var i=0; i<imageNumber; i++){
                    $("#editRequireDetail").find(".showImage")[0].remove();
                };
                var input=$("#editRequireDetail").find("input");
                var textarea=$("#editRequireDetail").find("textarea");
                for(var i=0; i<input.length; i++){
                    input.eq(i).val("")
                };
                for(var i=0; i<textarea.length; i++){
                    textarea.eq(i).val("")
                }
            })

            $("#orderDetail").on("click",".deleteOrder",function(event){                 //关闭交易
                event.preventDefault();
                event.stopImmediatePropagation();
                if(confirm("关闭交易是高危操作，请务必和买家充分沟通过再进行此操作！)")){
                    window.location.href="/operator/cancelOrder/"+$(".requirement_id").text();
                }
            })

            $("#requireDetail").on("click",".nextstep",function(event){           //下一步，拆单
                event.preventDefault();
                if($(".status").length==$(".saved").length){
                    var data={
                        "totalPrice":"",
                        "requirement_id":$("#requireDetail").find(".requirement_id").text()
                    };
                    var totalPrice=0;
                    for(var i=0; i<$(".price").length; i++){
                        if(parseFloat($(".price").eq(i).text())>0){
                            totalPrice=totalPrice+parseFloat($(".price").eq(i).text());
                        }
                    };
                    data.totalPrice=totalPrice;
                    console.log(data);
                    $.ajax({
                        url:"/operator/createMain",            //下一步拆单的请求接收地址
                        type:"post",
                        dataType:"json",
                        data:data,            //需求号，红领巾ID，总价
                        beforeSend: function (xhr) {
                            var token = $("meta[name=csrf-token]").attr('content');
                            if (token) {
                                return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                            }
                        },
                        success: function (response){
                            window.location.href=response;
                        },
                        error: function (request,errorType,errorMessage){
                            alert("error:"+errorType+";  message:"+errorMessage);
                        }
                    })
                }else{
                    alert("请保存当前页下所有商品")
                }

            })


        })