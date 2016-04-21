<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seckill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id', 'activity_id', 'start_time', 'due_time',
        'is_available', 'employee_id'
    ];
    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id');
    }

    public function activity()
    {
        return $this->belongsTo('App\Models\Activity');
    }

}
