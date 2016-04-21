<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubOrder extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'sub_order_id';
    protected $dates = ['deleted_at', 'created_offer_time',  'delivery_time', 'audit_passed_time'];

    protected $fillable = ['sub_order_id', 'main_order_id', 'postage','seller_id',
        'item_id', 'sku_info', 'sub_order_state', 'is_available',
        'deliver_info_id', 'sub_order_price', 'country_id',
        'sub_order_number',
        'charge_id', 'receiving_addresses_id','delivery_info_id',
        'operator_id','order_type', 'refund_price', 'buyer_id', 'created_offer_time'
    ];


    public function items()
    {
        return $this->belongsToMany('App\Models\Item');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\Employee', 'operator_id');
    }

    public function mainOrder()
    {
        return $this->belongsTo('App\Models\MainOrder','main_order_id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\Models\User', 'buyer_id');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country');
    }

    public function seller() {
        return $this->belongsTo('App\Models\Seller');
    }

    public function receivingAddress()
    {
        return $this->belongsTo('App\Models\ReceivingAddress','receiving_addresses_id');
    }

    public function payment()
    {
        return $this->belongsTo('App\Models\PaymentMethod','payment_methods_id');
    }

    public function activities()
    {
        return $this->belongsToMany('App\Models\Activity');
    }

    public function refunds(){
        return $this->hasMany('App\Models\OrderRefund');
    }

    public function deliveryInfo() {
        return $this->belongsTo('App\Models\DeliveryInfo','delivery_info_id');
    }

    public function subOrderMemos()
    {
        return $this->hasMany('App\Models\SubOrderMemo');
    }


    public function transferReason()
    {
        return $this->hasOne('App\Models\TransferReason');
    }

    public function snapshot()
    {
        return $this->hasOne('App\Models\SubOrderSnapshot');
    }

    public function groupItems()
    {
        return $this->hasMany('App\Models\GroupItem');
    }

    public function scopeWaitPay($query)
    {
        return $query->where('sub_order_state',201);
    }

    public function scopeWaitOffer($query)
    {
        return $query->where('sub_order_state',101);
    }

    public function scopeWaitDelivery($query)
    {
        return $query->where('sub_order_state',501);
    }

    public function scopeHasDelivered($query)
    {
        return $query->where('sub_order_state',601);
    }

    public function scopeHasFinished($query)
    {
        return $query->where('sub_order_state',301)->where('hide', false);
    }

    public function scopeOrderClosed($query)
    {
        return $query->where('sub_order_state',411)->orWhere('sub_order_state',431)->orWhere('sub_order_state',441);
    }

    public function scopeWaitAuditing($query)
    {
        return $query->where('sub_order_state',521);
    }

    public function scopeSellerAssign($query)
    {
        return $query->where('sub_order_state',241)->orWhere('sub_order_state',541);
    }

    public function scopeCanPay($query)
    {
        return $query->where('sub_order_state', 201)->orWhere('sub_order_state', 241);
    }

    public function scopeNeedToDeliver($query)
    {
        return $query->where('sub_order_state', 501)->orWhere('sub_order_state', 541);
    }

    public function scopeDelivered($query)
    {
        return $query->where('sub_order_state', 601)->orWhere('sub_order_state', 521);
    }
}
