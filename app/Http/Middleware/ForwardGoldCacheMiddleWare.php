<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Models\TaskOperation;
use Illuminate\Support\Facades\Log;

class ForwardGoldCacheMiddleWare
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
            // 此处通过请求判断加入缓存的类型
            if($request->optaskid && $request->isNewSupporter) {

                $user = Auth::user();
                $master = User::where('openid',$request->sender)->first();

                // 获得转发任务对象
                $operatorTask = TaskOperation::find($request->optaskid);
                $newTask = array(
                    $request->sender => array(
                        $user,
                        $master,
                        $operatorTask)
                );
                // 缓存已经有记录
                if($information = Cache::get($user->openid. '_forward_infos')) {
                    array_merge($information, $newTask);
                    Cache::forever($user->openid. '_forward_infos', $information);
                }
                else {
                    Cache::forever($user->openid. '_forward_infos', $newTask);
                }
            }
        return $next($request);
    }
}
