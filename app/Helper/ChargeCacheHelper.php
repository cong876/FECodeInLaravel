<?php


namespace App\Helper;

use Illuminate\Support\Facades\Cache;
use Pingpp\Charge;
use Pingpp\Pingpp;


class ChargeCacheHelper extends SingleBase
{
    public function getRegionByChargeId($chargeId)
    {
        return Cache::get('ChargePXX_'. $chargeId, function() use ($chargeId) {
            return  Cache::rememberForever('ChargePXX_'. $chargeId, function() use ($chargeId) {
                Pingpp::setApiKey(config('pingpp.apiKey'));
                $charge = Charge::retrieve($chargeId);
                return $charge;
            });
        });
    }

}