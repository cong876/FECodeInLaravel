<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CandyPromotionMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if($user->is_subscribed == 0) {
            // 缓存推入链接地址
            $openId = $user->openid;
            $expiresAt = \Carbon\Carbon::now()->addHour(24);
            $expiresAtMinute = \Carbon\Carbon::now()->addMinutes(5);

            $info = array(
                'url' => url('buyPal/buy#candy'),
                'title' => '红领巾｜一分钱换国外零食礼包！',
                'description' => '1、点击【帮我代购】，享受【首单全球免邮】，红领巾能够为您代购任何国家的任何商品(☆_☆)/~~
2、点击【个人中心-今日团购】，查看团购商品。',
                'imgUrl' => 'http://7xln8l.com2.z0.glb.qiniucdn.com/dituiweixintoutu.jpg'
            );

            Cache::put($openId."_candy_before_subscribe_info", json_encode($info), $expiresAtMinute);
            Cache::put($openId."_candy_available", true, $expiresAt);
            Cache::put($openId."_is_candy", true , $expiresAtMinute);
            return redirect('http://mp.weixin.qq.com/s?__biz=MzIyMDA1MDU4Mw==&mid=400041637&idx=1&sn=b928cf53b9c419c376635f567431af90&scene=0#rd');
        }
        return $next($request);
    }
}
