<div class="query panel-body" style="background-color: #f0f0f0">
    <form class="form-horizontal" role="form" method="get" action="{{url('operator/activitySearch')}}">

        <div class="col-lg-4 col-md-4">

            <div class="form-group input-group-sm col-lg-8 col-md-8">
                <input type="date" class="form-control" name="activitiesTime" id="activitiesTime">
            </div>
            <input class="hidden" name="page" value="1">
        </div>

    </form>
</div>

<script type="text/javascript">
    $("#activitiesTime").on("change",function(event){
        event.preventDefault();
        $(this).parents("form").submit();
    })
</script>