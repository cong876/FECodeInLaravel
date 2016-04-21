<?php


namespace App\Helper;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/*
 * $instance = GetBuyerAverage::getInstance();
 * $instance->getPaidCountAverage();
 * $instance->getPaidAmountAverage();
 */

class GetBuyerAverage extends SingleBase
{
    /**
     * @return mixed
     */
    public function getPaidCountAverage()
    {
        return Cache::get('YeBuyersPaidCountAverage', function() {
            return  Cache::remember('YeBuyersPaidCountAverage', 720 ,function(){
                return DB::table('buyers')->avg('buyer_paid_count');
            });
        });
    }

    public function getPaidAmountAverage()
    {
        return Cache::get('YeBuyersPaidAmountAverage', function() {
            return  Cache::remember('YeBuyersPaidAmountAverage', 720 ,function(){
                return DB::table('buyers')->avg('buyer_initial_paid');
            });
        });
    }

}