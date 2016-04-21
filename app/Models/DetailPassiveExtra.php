<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPassiveExtra extends Model
{

    protected $primaryKey = 'detail_passive_id';

    protected $fillable = ['item_id', 'description'];

    public function item() {
        return $this->belongsTo('App\Models\Item');
    }

}
