<?php

namespace App\Listeners;

use App\Events\MailToSellerForDeliverEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SubOrder;
use Illuminate\Support\Facades\Event;

class MailToSellerForDeliverListener implements ShouldQueue
{

    use InteractsWithQueue;

    public function __construct()
    {

    }


    public function handle(MailToSellerForDeliverEvent $event)
    {
        $send_time = $event->send_time;
        $send_count = $event->send_count;
        $subOrder = SubOrder::where('sub_order_number', $event->order_number)->first();

        if(!$subOrder) {
            $this->delete();
        }
        if ($subOrder->sub_order_state == 601 || $subOrder->sub_order_state == 521) {
            $this->delete();
        }


        if ($subOrder->sub_order_state == 541) {
            $this->delete();
        }

        // 原事件传递的邮箱
        $oriMail = $event->mail;
        $sellerEmail = $subOrder->seller->user->email;
        if($oriMail != $sellerEmail) {
            $this->delete();
            return false;
        }


        if ($send_count == 1) {
            // 第一次立即发送
            $result = $this->sendMail(
                [$event->mail],
                [$event->order_number],
                [$event->item_title],
                [$event->price],
                [$event->payment_time],
                [$event->name],
                [$event->mobile],
                [$event->receiving_address],
                [$event->zip_code]
            );
            if($result) {
                // 发送成功
                $newCount =  $event->send_count + 1;
                Event::fire(new MailToSellerForDeliverEvent(
                    $event->mail,
                    $event->order_number,
                    $event->item_title,
                    $event->price,
                    $event->payment_time,
                    $event->name,
                    $event->mobile,
                    $event->receiving_address,
                    $event->zip_code,
                    $event->send_time,
                    $newCount
                ));
            }else {
                echo "发送失败";
            }

        }
        if($send_count == 2) {
            $diffTime = time() - $send_time;// 当前时间 - $send_time > 12 * 3600;
            if($diffTime > 3600) {
                $result = $this->sendMail(
                    [$event->mail],
                    [$event->order_number],
                    [$event->item_title],
                    [$event->price],
                    [$event->payment_time],
                    [$event->name],
                    [$event->mobile],
                    [$event->receiving_address],
                    [$event->zip_code]
                );
                if($result) {
                    // 发送成功
                    $newCount =  $event->send_count + 1;
                    Event::fire(new MailToSellerForDeliverEvent(
                        $event->mail,
                        $event->order_number,
                        $event->item_title,
                        $event->price,
                        $event->payment_time,
                        $event->name,
                        $event->mobile,
                        $event->receiving_address,
                        $event->zip_code,
                        time(), // 发送时间
                        $newCount
                    ));
                }else {
                    echo "发送失败";
                }
            }
            else {
                $this->release(24 * 4 * 3600);
            }
        }
        if($send_count == 3 || $send_count == 4) {
            $diffTime = time() - $send_time;// 当前时间 - $send_time > 12 * 3600;
            if($diffTime > 3600) {
                $result = $this->sendMail(
                    [$event->mail],
                    [$event->order_number],
                    [$event->item_title],
                    [$event->price],
                    [$event->payment_time],
                    [$event->name],
                    [$event->mobile],
                    [$event->receiving_address],
                    [$event->zip_code]
                );
                if($result) {
                    // 发送成功
                    $newCount =  $event->send_count + 1;
                    Event::fire(new MailToSellerForDeliverEvent(
                        $event->mail,
                        $event->order_number,
                        $event->item_title,
                        $event->price,
                        $event->payment_time,
                        $event->name,
                        $event->mobile,
                        $event->receiving_address,
                        $event->zip_code,
                        time(), // 发送时间
                        $newCount
                    ));
                }else {
                    echo "发送失败";
                }
            }
            else {
                $this->release(24 * 3600);
            }
        }

        if($send_count == 5) {
            // 删除任务
            $this->delete();
            // 写日志
        }
    }

    private function sendMail($mail, $order_number, $item_title,
                              $price, $payment_time, $name,
                              $mobile, $receiving_address, $zip_code)
    {
        $API_USER = 'YeYeTech_Notify';
        $API_KEY = '9fyh416OvSFhXYEY';
        $ch = curl_init();
        $vars = json_encode(array("to" => $mail,
                "sub" => array("%order_number%" => $order_number,
                    "%item_title%" => $item_title, "%order_price%" => $price,
                    "%payment_time%" => $payment_time, "%name%" => $name,
                    "%mobile%" => $mobile, "%receiving_address%" => $receiving_address,
                    "%zip_code%" => $zip_code
                )
            )
        );

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://sendcloud.sohu.com/webapi/mail.send_template.json');

        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from' => 'service@yeyetech.net',
            'fromname' => '红领巾小助手',
            'use_maillist' => 'false',
            'substitution_vars' => $vars,
            'template_invoke_name' => 'delivery_notify',
            'subject' => '红领巾通知：买家已付款，请发货',
            'html' => "欢迎使用红领巾",
        ));

        $result = curl_exec($ch);

        if ($result === false) {
            return false;
        }
        curl_close($ch);
        return true;
    }
}
