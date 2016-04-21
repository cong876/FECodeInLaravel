<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
    //
    protected $primaryKey = "order_refund_id";

    protected $fillable = ['refund_type', 'item_id', 'refund_inventory_count', 'refund_price',
        'ppp_status', 'charge_id', 'sub_order_id', 'ppp_refund_order_id','is_successful', 'description',
        'refund_success_time'];

    public function subOrder()
    {
        return $this->belongsTo('App\Models\SubOrder', 'sub_order_id');
    }


}