<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointStatement extends Model
{
    protected $fillable = ['task_id','task_name','points',
        'task_type','hlj_id','multiplier','positive'];
}
