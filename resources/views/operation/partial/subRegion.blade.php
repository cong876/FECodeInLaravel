@foreach($cities as $city)
<?php if(count($city)!=0){ ?>
<option value="{{$city->code}}">{{$city->name}}</option>
<?php }
else{
?>
<option value="1">不用选啦</option>
<?php }?>
@endforeach
