<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryCompany extends Model
{
    //
    protected  $primaryKey = 'delivery_company_id';

    protected $fillable = ['company_name','pinyin'];

    public function deliveryInfo()
    {
        return $this->belongsTo('App\Models\DeliveryInfo','delivery_info_id');
    }
}
