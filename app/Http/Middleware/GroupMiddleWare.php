<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class GroupMiddleWare
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

            $info = array(
                'url' => url('getPeriodActivity'),
                'title' => $request->title,
                'description' => $request->description,
                'imgUrl' => $request->imgUrl
            );

            Cache::put($openId."_group_before_subscribe_info", json_encode($info), $expiresAt);
            Cache::put($openId."_is_group", true , $expiresAt);
        }
        return $next($request);
    }
}
