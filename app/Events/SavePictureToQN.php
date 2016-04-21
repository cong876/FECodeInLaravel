<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SavePictureToQN extends Event
{
    use SerializesModels;

    public $url;
    public $bucket;
    public $key;

    public function __construct($url, $bucket, $key)
    {
        $this->url = $url;
        $this->bucket = $bucket;
        $this->key = $key;
    }

    public function broadcastOn()
    {
        return [];
    }
}
