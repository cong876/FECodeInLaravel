<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $primaryKey = 'buyer_id';
    protected $fillable = ['hlj_id','buyer_memo','buyer_success_orders_num','buyer_success_paid','buyer_gmv','is_available'];
    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','hlj_id');
    }

    public function buyerMemos()
    {
        return $this->hasMany('App\Models\BuyerMemo');
    }

    public function scopeAvailableBuyer($query)
    {
        return $query->where('is_available',true);
    }
}
