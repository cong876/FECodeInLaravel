<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class WXAccessToken
{
    public static function getToken()
    {
        $cacheKey = 'YeYe_AccessToken';
        return Cache::get($cacheKey, function() use($cacheKey){
            $appId  = config('wx.appId');
            $secret = config('wx.appSecret');
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $secret;
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            $token = $data["access_token"];
            $expiresAt = Carbon::now()->addSeconds($data['expires_in'] - 3333);
            // 缓存Token
            Cache::put($cacheKey, $token, $expiresAt);
            return $token;
        });
    }

    public static function forgetToken()
    {
        Cache::forget('YeYe_AccessToken');
    }
}