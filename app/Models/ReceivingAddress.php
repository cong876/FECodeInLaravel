<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivingAddress extends Model
{
    use softDeletes;

    protected $primaryKey = 'receiving_addresses_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['hlj_id','receiver_name','receiver_mobile','receiver_zip_code','country_id','first_class_area',
    'second_class_area','third_class_area','street_address','is_default','is_available'];
    protected $casts = [
        'is_available' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'hlj_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }


}
