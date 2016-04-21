<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainOrder extends Model
{
    //
    use SoftDeletes;

    protected $primaryKey = "main_order_id";
    protected $dates = ['deleted_at'];

    protected $fillable = ['main_order_id', 'main_order_type', 'hlj_id', 'receiving_addresses_id', 'main_order_status',
        'main_order_price'];

    public function requirement() {
        return $this->hasOne('App\Models\Requirement');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Item');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'hlj_id');
    }

    public function subOrders()
    {
        return $this->hasMany('App\Models\SubOrder');
    }

    public function ScopeWaitSendPrice($query)
    {
        return $query->where('main_order_state',201);
    }
}
