<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $primaryKey = 'seller_id';
    protected $fillable = ['hlj_id', 'seller_type', 'seller_service_area', 'seller_memo', 'seller_refuse_orders_num',
        'seller_success_orders_num', 'seller_success_incoming', 'seller_gmv',
        'is_available','real_name','country_id','name_pinyin','name_abbreviation'];
    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','hlj_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function sellerMemos()
    {
       return $this->hasMany('App\Models\SellerMemo');
    }

    public function sellerPrison()
    {
        return $this->hasOne('App\Models\SellerPrison');
    }

    /*
     *
     * 查询当前国家的所有买手
     *
     */
    public function ScopeSearchSeller($query,$country_id)
    {
        return $query->where('country_id',$country_id)->where('is_available',true);
    }

    public function ScopeAvailableSeller($query)
    {
        return $query->where('is_available',true);
    }

}
