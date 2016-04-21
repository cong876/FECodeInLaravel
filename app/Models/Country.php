<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $primaryKey = 'country_id';

    public function items() {
        return $this->hasMany('App\Models\Item');
    }

    public function sellers()
    {
        return $this->hasMany('App\Models\Seller');
    }
}
