<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MailToSellerForDeliverEvent extends Event
{
    use SerializesModels;

    public $mail;
    public $order_number;
    public $item_title;
    public $price;
    public $payment_time;
    public $name;
    public $mobile;
    public $receiving_address;
    public $zip_code;
    public $send_time;
    public $send_count;

    // 构造方法传递事件
    public function __construct($mail , $order_number, $item_title,
                                $price, $payment_time, $name,
                                $mobile, $receiving_address, $zip_code,
                                $send_time, $send_count)
    {

        $this->mail = $mail;
        $this->order_number = $order_number;
        $this->item_title = $item_title;
        $this->price = $price;
        $this->payment_time = $payment_time;
        $this->name = $name;
        $this->mobile = $mobile;
        $this->receiving_address = $receiving_address;
        $this->zip_code = $zip_code;
        $this->send_time = $send_time;
        $this->send_count = $send_count;
    }


    public function broadcastOn()
    {
        return [];
    }
}
