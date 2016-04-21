<?php

namespace App\Listeners;

use App\Events\UserTalkedThroughWeChat;
use App\Utils\ThirdParty\WilddogLib;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastToOperator implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {

    }


    public function handle(UserTalkedThroughWeChat $event)
    {
        // 推动到野狗云
        $user = $event->user;
        $wildDog = new WilddogLib('https://buypal.wilddogio.com/', 'tjXo3cQBnYkULU71ngriY5wKlq2QcUsVSRE0Qxnh');

        if ($wildDog->get('users/'. $user->openid . '/info') == "null") {
            $wildDog->set('users/'. $user->openid . '/info', [
                'headimgurl' => $user->headimgurl,
                'sex' => $user->sex,
                'nickname' => $user->nickname,
                'province' => $user->province,
                'mobile' => $user->mobile,
                'city' => $user->city
            ]);
        }
        $wildDog->set('users/'. $user->openid . '/updated_at', date('Y-m-d H:i:s'));
        $wildDog->push('users/'. $user->openid . '/messages', [
            'message' => $event->message
        ]);
    }
}
