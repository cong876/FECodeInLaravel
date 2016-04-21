<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaskOperation extends AbstractTask
{
    use SoftDeletes;

    protected $primaryKey = 'task_id';

    protected $fillable = ['task_id','task_name','coins','task_type','is_increased'];

}
