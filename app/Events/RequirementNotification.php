<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequirementNotification extends Event
{
    use SerializesModels;

    public $user;
    public $requirement;
    public $items;

    public function __construct($user, $requirement, $items)
    {
        $this->user = $user;
        $this->requirement = $requirement;
        $this->items = $items;
    }
}
