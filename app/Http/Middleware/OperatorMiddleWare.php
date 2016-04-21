<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OperatorMiddleWare
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
        $op_level = Session::get('op_level');

        if(empty($op_level)) {
            abort(401);
        }else {
            if($op_level < 3) {
                abort(451);
            }
            return $next($request);
        }
    }
}
