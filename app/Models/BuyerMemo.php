<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerMemo extends Model
{
    //
    use SoftDeletes;

    protected $primaryKey = 'buyer_memo_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['buyer_id','content','hlj_id','created_at'];

    public function buyer()
    {
        return $this->belongsTo('App\Models\Buyer','buyer_id');
    }

}
