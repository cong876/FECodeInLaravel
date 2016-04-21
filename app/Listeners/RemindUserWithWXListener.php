<?php

namespace App\Listeners;

use App\Events\RemindUserWithWX;
use Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helper\WXNotice;

class RemindUserWithWXListener implements ShouldQueue
{

    use InteractsWithQueue;

    public function __construct()
    {

    }

    /**
     * 向用户推送秒杀前提醒
     *
     * @param  RemindUserWithWX  $event
     * @return void
     */
    public function handle(RemindUserWithWX $event)
    {
        $open_id = $event->open_id;
        $secKill_id = $event->secKill_id;

        $remindKey = 'User:'. $open_id . ':SecKill:'. $secKill_id . ':Remind';
        $prefix = $event->prefix;
        $title = $event->title;
        $time = $event->time;
        $diff = $time - time();

        if($diff > 6 * 60) {
            $this->release($diff - 5 * 60);
        }else {
            $timeStr = date("H:i:s", $time). '开始';
            if(Cache::get($remindKey)) {
                $notice = new WXNotice();
                $notice->notifyBuyerActivityWillStarted($open_id, $prefix, $title, $timeStr);
                Cache::forget($remindKey);
            }
            $this->delete();
        }
    }
}
