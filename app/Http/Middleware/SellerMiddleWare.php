<?php

// 通过微信登录买手后台

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class SellerMiddleWare
{
    public function handle($request, Closure $next)
    {

        // 用户存在
        $user = Auth::user();
        $seller = $user->seller;
        $sellerRole = Session::get('sellerRole');
        if(!empty($seller) && !empty($sellerRole)) {
            return $next($request);
        }
        else {
            abort(461);
        }

    }
}