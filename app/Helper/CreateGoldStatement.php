<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/11/6
 * Time: 下午12:16
 */
namespace App\Helper;

use App\Models\TaskOperation;
use App\Models\TaskItem;
use App\Models\GoldStatements;
use App\Models\TaskInterface;
use App\Models\User;

class CreateGoldStatement
{
    protected $user;

    function __construct(User $user)
    {
        $this->user = $user;
    }

    public function decrease(TaskInterface $task, User $user, $number) {
        $coins= $task->getTaskCoins()*$task->isIncrease()*$number;
        $name = $task->getTaskName();
        $type = $task->getTaskType();
        $this->user->statements()->create(array('task_id'=>$task->getTaskId(), 'task_name' => $name, 'coins' => $coins,
            'task_type' => $type, 'hlj_id' => $user->hlj_id,'is_increased' => $task->isIncrease(),'num' => $number));
        $user->golds->current_gold_num += $coins;
        if($coins>0)
        {
            $user->golds->total_gold_received += $coins;
        }
        $user->golds->save();
    }

    public function increase(TaskInterface $task, User $user, $master, $gold) {
        $coins_each= $task->getTaskCoins();
        $name = $task->getTaskName();
        $type = $task->getTaskType();
        if($gold > $coins_each)
        {
            $coins = $coins_each;
        }
        else
        {
            $coins = $gold;
        }
        $this->user->statements()->create(array('task_id'=>$task->getTaskId(), 'task_name' => $name, 'coins' => $coins,
            'task_type' => $type, 'hlj_id' => $user->hlj_id,'is_increased' => $task->isIncrease(),'num' => 1));
        $master->golds->current_gold_num += $coins;
        $master->golds->total_gold_received += $coins;
        $master->golds->save();
    }





}