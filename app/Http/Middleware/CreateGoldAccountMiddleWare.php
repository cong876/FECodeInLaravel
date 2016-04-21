<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Gold;

class CreateGoldAccountMiddleWare
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
        if(count($user->golds) == 0) {
            $gold = new Gold;
            $gold->hlj_id = $user->hlj_id;
            $gold->save();
        }
        return $next($request);
    }
}
