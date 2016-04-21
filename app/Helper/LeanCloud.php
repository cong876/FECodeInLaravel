<?php

namespace App\Helper;

class LeanCloud
{
    // 买家通知短信
    public static function buyerRequestSuccessSMS($mobile, $request)
    {
        $data = [
            "mobilePhoneNumber" => $mobile,
            "template" => "成功提交需求",
            "request" => $request,
        ];
        LeanCloud::sendLeanCloudSMS($data);
    }

    public static function sendBuyerPayNotification($mobile, $itemTitle, $supportPhone)
    {
        $data = [
            "mobilePhoneNumber" => $mobile,
            "template" => "请付款",
            "item_title" => $itemTitle,
            "expire_time" => 48,
            "support_phone" => $supportPhone,
        ];
        LeanCloud::sendLeanCloudSMS($data);
    }

    public static function sellerDeliverItemsSMS($mobile, $itemTitle, $supportPhone)
    {
        $data = [
            "mobilePhoneNumber" => $mobile,
            "template" => "已发货",
            "item_title" => $itemTitle,
            "support_phone" => $supportPhone,
        ];
        LeanCloud::sendLeanCloudSMS($data);
    }

    public static function refundBuyer($mobile, $refund_num, $orderTail, $supportPhone)
    {
        $data = [
            "mobilePhoneNumber" => $mobile,
            "template" => "退款_买家",
            "refund_num" => $refund_num,
            "order_tail" => $orderTail,
            "support_phone" => $supportPhone,
        ];
        LeanCloud::sendLeanCloudSMS($data);
    }


    private static function sendLeanCloudSMS($data)
    {
        $url = "https://api.leancloud.cn/1.1/requestSmsCode";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-AVOSCloud-Application-Id: 1uCLCKq2T7Y4jh0VxXgBOLpV',
            'X-AVOSCloud-Application-Key: Sh2vHA09uWO21vuvBCTL8bop', 'Content-Type: application/json'));
        $ret = curl_exec($ch);
    }
}