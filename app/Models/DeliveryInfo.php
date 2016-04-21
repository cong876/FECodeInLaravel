<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryInfo extends Model
{
    //
    protected $primaryKey = 'delivery_info_id';

    protected $fillable = ['sub_order_id','delivery_order_number','delivery_company_id','delivery_company_info','delivery_related_url'];

    public function subOrder()
    {
        return $this->belongsTo('App\Models\SubOrder','sub_order_id');
    }

    public function deliveryCompany()
    {
        return $this->belongsTo('App\Models\DeliveryCompany','delivery_company_id');
    }
}
