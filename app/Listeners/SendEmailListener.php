<?php

namespace App\Listeners;

use App\Events\SendEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailListener
{
//    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendEmail  $event
     * @return void
     */
    public function handle(SendEmail $event)
    {
        //
        $data = ['email'=>'shimeng.wang09@hotmail.com','name'=>'Shimeng Wang'];
        \Mail::send('mail.sendPrice',$data,function($message) use($data)
        {
//            $message->from('542976414@qq.com', 'Laravel');
            $message->to($data['email'],$data['name'])->subject('Price Tips');
        });
    }
}
