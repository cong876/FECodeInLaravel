<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerMemo extends Model
{
    //
    use softDeletes;

    protected $primaryKey = 'seller_memo_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['hlj_id','content','seller_id','created_at'];

    public function Seller()
    {
        return $this->belongsTo('App\Models\Seller','seller_id');
    }
}
