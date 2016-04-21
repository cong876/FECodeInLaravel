<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cache;


class SellerUseOnce
{
    public function generateRegisterCode()
    {
        $code =  bcrypt(time().'YeYe');
        $expiresAt = \Carbon\Carbon::now()->addHours(24);
        Cache::put($code, '', $expiresAt);
        return $code;
    }

    public function bindRegisterCodeToOpenId($code, $openId)
    {
        $expiresAt = \Carbon\Carbon::now()->addHour(24);
        Cache::put($openId."_seller_register_code", $code, $expiresAt);
        Cache::put($code, $openId, $expiresAt);
    }

    public function killTheUsedCode($openId)
    {
        $code = Cache::get($openId."_seller_register_code");
        Cache::forget($code);
        Cache::forget($openId."_seller_register_code");
    }

}