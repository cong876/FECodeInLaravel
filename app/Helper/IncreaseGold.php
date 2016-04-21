<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/11/6
 * Time: 下午9:03
 */
namespace App\Helper;

use App\Helper\WXNotice;
use App\Helper\GoldFormula;

class IncreaseGold
{

    public function addGold($user, $master,$taskOperation)
    {
        if ($user->golds->limited_support_times < 2) {
            $statement = new CreateGoldStatement($master);
            if($user->hlj_id > 9) {
                $user->golds->limited_support_times += 1;
                $user->golds->save();
            }
            $formula = new GoldFormula();
            if($master->buyer)
            {
                $coins = $formula->getTodayGold($master->buyer->buyer_initial_paid,$master->buyer->buyer_paid_count,25
                    ,2,2,40,20);
            }
            else
            {
                $coins = $formula->getTodayGold(0,0,25,2,2,40,20);
            }
            $statement->increase($taskOperation, $user, $master, $coins);
            if($coins>200)
            {
                $gold = 200;
            }
            else
            {
                $gold = $coins;
            }
            $notice = new WXNotice();
            $notice->addStars($master->openid,$user->nickname,$gold,$master->golds->current_gold_num);
            return true;
        }
        else {
            return false;
        }
    }
}
