<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperMiddleWare
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
        // 用户存在
        $user = Auth::user();
        $open_id = $user->openid;
        if($this->isSuper($open_id)) {
            return $next($request);
        }else {
            abort(471);
        }
    }

    private function isSuper($openId)
    {
        $supers = [
            'olxLuv7ftcxC48-YGe6go_E-0FMo',
            'olxLuv5iW-J-1xNhpYrFY87x7v8Q'
        ];
        return in_array($openId, $supers);
    }
}
