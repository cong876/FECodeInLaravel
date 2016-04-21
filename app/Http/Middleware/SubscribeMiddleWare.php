<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class SubscribeMiddleWare
{
    /**
     * 该中间件判断当前用户是否关注过公众号
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if($user->is_subscribed == 0) {
            if($request->path() == 'accessGoldActivity') {
                // 特殊二维码(关注后分组到转发)
                return redirect('http://mp.weixin.qq.com/s?__biz=MzIyMDA1MDU4Mw==&mid=400215067&idx=1&sn=2376c25c614d11d08dce51044f5d9e77&scene=0#wechat_redirect');
            }
        }
        return $next($request);
    }
}
