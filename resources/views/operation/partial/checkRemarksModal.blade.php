    <div class="modal fade" id="checkRemarks" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 400px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                      &times;
                    </button>
                <h4 class="modal-title">
                    订单备注：
                </h4>
                </div>
                <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                    <ul class="remarks">
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#order,#seller,#buyer,#remittanceHeader").on("click",".requirement_memo,.order_memo,.buyer_memo,.seller_memo",function(event){
                event.preventDefault();
                if($(this).hasClass("requirement_memo")){
                    $("#checkRemarks").find(".modal-title").text("需求备注");
                }else if($(this).hasClass("buyer_memo")){
                    $("#checkRemarks").find(".modal-title").text("买家备注");
                }else if($(this).hasClass("seller_memo")){
                    $("#checkRemarks").find(".modal-title").text("买手备注");
                };
                var memoData=$(this).data("memo");
                var memo="";
                for(var i=$(this).data("memo").length; i>0; i--){
                    memo += '<li><p><span>备注时间：</span><span>'
                        +memoData[i-1][0].date.substring(memoData[i-1][0].date.indexOf("."),-7)
                        +'</span><span>  备注人：</span><span>'
                        +memoData[i-1][2]
                        +'</span><span class="placeholder">占位</span></p><p>'
                        +memoData[i-1][1]
                        +'</p></li>'
                }
                $("#checkRemarks").find(".remarks").html(memo);
            })

        })
    </script>