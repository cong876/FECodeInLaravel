<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaskItem extends AbstractTask
{
    use SoftDeletes;

    protected $primaryKey = 'task_id';

    protected $fillable = ['task_id','task_name','coins','task_type','item_id','is_increased'];

    public function items()
    {
        return $this->belongsTo('App\Models\Item');
    }
}
