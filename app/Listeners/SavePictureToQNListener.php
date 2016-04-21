<?php

namespace App\Listeners;

use App\Events\SavePictureToQN;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Qiniu\Auth as Qiniu;
use Qiniu\Storage\BucketManager;

class SavePictureToQNListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {

    }


    public function handle(SavePictureToQN $event)
    {
        $url = $event->url;
        $bucket = $event->bucket;
        $key = $event->key;
        $auth = new Qiniu(config('qiniu.accessKey'), config('qiniu.secretKey'));
        $bm = new BucketManager($auth);
        $bm->fetch($url, $bucket, $key);
    }
}
