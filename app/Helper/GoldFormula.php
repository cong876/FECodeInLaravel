<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/11/12
 * Time: 上午10:45
 */
namespace App\Helper;

class GoldFormula
{
    public function getTodayGold($user_coins,$user_num,$least_coin,$base_coin,$base_num,$constant_coin,$constant_num)
    {
        $data = GetBuyerAverage::getInstance();
        $num_ave = $data->getPaidCountAverage();
        $coins_ave = $data->getPaidAmountAverage();
        $coins = $least_coin + $constant_coin * log(($user_coins+$coins_ave)/$coins_ave,$base_coin)
            +$constant_num*log(max($user_num,$num_ave)/$num_ave,$base_num);
        return floor($coins);
    }
}