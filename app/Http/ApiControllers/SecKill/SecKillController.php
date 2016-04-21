<?php


namespace App\Http\ApiControllers\SecKill;

use App\Events\RemindUserWithWX;
use App\Http\ApiControllers\Controller;
use App\Models\Seckill;
use App\Utils\Json\ResponseTrait;
use Cache;
use Carbon\Carbon;
use DB;


class SecKillController extends Controller
{
    use ResponseTrait;

    public function remind($secKill_id, $user_id)
    {
        $user = DB::table('users')->where('hlj_id', $user_id)->first();
        $remindKey = 'User:'. $user->openid . ':SecKill:'. $secKill_id . ':Remind';
        $secKill = Seckill::find($secKill_id);
        $title = '【1元秒杀】' . $secKill->item->title;
        $time = strtotime($secKill->start_time);
        $prefix = "1元秒杀，马上开始！\n";
        \Event::fire(new RemindUserWithWX($user->openid, $prefix, $title, $time, $secKill_id));
        $expire_at = Carbon::now()->addHours(48);
        Cache::put($remindKey, 1, $expire_at);
        $ret = $this->requestSucceed();
        return $this->response->array($ret);
    }

    public function cancelRemind($secKill_id, $user_id)
    {
        $user = DB::table('users')->where('hlj_id', $user_id)->first();
        $remindKey = 'User:'. $user->openid . ':SecKill:'. $secKill_id . ':Remind';
        Cache::forget($remindKey);
        $ret = $this->requestSucceed();
        return $this->response->array($ret);
    }

}