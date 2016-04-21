<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cache;
use App\Models\ChinaRegion;


class ChinaRegionsHelper extends SingleBase
{
    public function getRegionByCode($code)
    {
        return Cache::get('ChinaRegion_'. $code, function() use ($code) {
            return  Cache::rememberForever('ChinaRegion_'. $code, function() use ($code) {
                return ChinaRegion::where('code', $code)->first();
            });
        });
    }
}