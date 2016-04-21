<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubOrderMemo extends Model
{
    //
    use SoftDeletes;

    protected $primaryKey = 'sub_order_memo_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['hlj_id','sub_order_id','content','created_at'];

    protected $hidden = ['sub_order_memo_id','updated_at'];

    public function subOrders()
    {
        return $this->belongsTo('App\Models\SubOrder','sub_order_id');
    }

    public function scopeSearchOrder($query,$sub_order_id)
    {
        return $query->where('sub_order_id',$sub_order_id);
    }
}
