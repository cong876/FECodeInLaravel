<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Supporter;
use App\Models\Relations;

class ForwardMiddleware
{
    /**
     * 该中间件建立转发关系和朋友关系,标识转发行为中的转发人,打开人,记录是否是新转发行为
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if($request->sender) {
            $master = User::where('openid',$request->sender)->first();
        }
        // 判断当前用户是否帮助过链接转发人
        if(isset($master) && ($user->openid != $master->openid) && count($master->supporters()->where('support_id', $user->hlj_id)->get()) == 0) {
            // 建立转发关系, 附带请求参数
            $support = new Supporter();
            $support->master_id = $master->hlj_id;
            $support->support_id = $user->hlj_id;
            $support->save();
            $request->isNewSupporter = true;

            // 判断是否是朋友
            if(count($master->friends()->where('hlj_id', $user->hlj_id)->get()) == 0) {
                $relation = new Relations();
                $relation->user1_id = $master->hlj_id;
                $relation->user2_id = $user->hlj_id;
                $relation->save();
                $relation2 = new Relations();
                $relation2->user1_id = $user->hlj_id;;
                $relation2->user2_id = $master->hlj_id;;
                $relation2->save();
            }
        }
        return $next($request);
    }
}
