<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractTask extends Model{

    public function getTaskName()
    {
        return $this->task_name;
    }

    public function getTaskPoints()
    {
        return $this->points;
    }

    public function getTaskType()
    {
        return $this->getTable();
    }

    public function getTaskId()
    {
        return $this->id;
    }

    public function positive()
    {
        return boolval($this->positive);
    }
}