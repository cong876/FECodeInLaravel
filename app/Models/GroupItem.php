<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupItem extends Model
{
    //
    use SoftDeletes;

    protected $primaryKey = 'activity_id';

    protected $fillable = ['item_id','sub_order_id','number','memo','hlj_id'];

    public function subOrders()
    {
        return $this->belongsTo('App\Models\SubOrder','sub_order_id');
    }

}
