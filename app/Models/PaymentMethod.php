<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'payment_methods_id';
    protected $dates = ['deleted_at'];

    protected $fillable = ['hlj_id','channel','identification',
        'account_name','is_available','bank_card_bin_id','bank_card_number','is_default'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'is_available' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function bankInfo()
    {
        return $this->belongsTo('App\Models\BankCardBin', 'bank_card_bin_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'hlj_id');
    }


    public function getByBankCardNumber($number)
    {
        if(!empty(PaymentMethod::where('bank_card_number',$number)->first()))
        {
            return PaymentMethod::where('bank_card_number',$number)->first();
        }
    }

    public function scopeBank($query)
    {
        return $query->where('channel', 1);
    }

    public function scopeDefaultOne($query)
    {
        return $query->where('is_default', true);
    }


}
