<?php

namespace App\Listeners;

use App\Events\RequirementNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helper\WXNotice;
use App\Helper\LeanCloud;

class RequirementNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {

    }

    public function handle(RequirementNotification $event)
    {
        $user = $event->user;
        $requirement = $event->requirement;
        $items = $event->items;
        $title = $items->implode('title', ';');
        $title =  mb_strlen($title) > 8 ? mb_substr($title, 0, 8) . '...' : $title . '。';
        LeanCloud::buyerRequestSuccessSMS($user->mobile, $title);


        $notice = new WXNotice();
        $notice->buyerRequestSuccessNotice($user->openid, $requirement->requirement_number, $requirement->created_at);

    }
}
