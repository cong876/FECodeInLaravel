<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class RemindUserWithWX extends Event
{
    use SerializesModels;

    public $open_id;
    public $title;
    public $time;
    public $prefix;
    public $secKill_id;

    public function __construct($open_id, $prefix, $title, $time, $secKill_id)
    {
        $this->open_id = $open_id;
        $this->prefix = $prefix;
        $this->title = $title;
        $this->time = $time;
        $this->secKill_id = $secKill_id;
    }

}
