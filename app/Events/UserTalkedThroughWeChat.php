<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserTalkedThroughWeChat extends Event
{
    use SerializesModels;


    public $message;
    public $user;

    public function __construct($user, $message)
    {
        $this->message = $message;
        $this->user =$user;

    }
}
