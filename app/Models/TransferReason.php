<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferReason extends Model
{
    //
    use SoftDeletes;

    protected $primaryKey = 'transfer_reason_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['reason','sub_order_id'];

    public function subOrder()
    {
        return $this->belongsTo('App\Models\SubOrder','sub_order_id');
    }
}
