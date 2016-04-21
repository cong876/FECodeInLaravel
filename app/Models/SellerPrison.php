<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerPrison extends Model
{
    //
    use SoftDeletes;

    protected $primaryKey = 'seller_prison_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['seller_id','reasons','created_at','updated_at'];

    public function seller()
    {
        return $this->belongsTo('App\Models\Seller','seller_id');
    }

}
