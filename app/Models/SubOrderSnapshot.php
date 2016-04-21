<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubOrderSnapshot extends Model
{
    protected $primaryKey = 'order_snapshot_id';
    protected $fillable = ['sub_order_id', 'bid_snapshot', 'paid_snapshot'];

    public function suborder()
    {
        return $this->belongsTo('App\Models\SubOrder', 'sub_order_id');
    }

}
