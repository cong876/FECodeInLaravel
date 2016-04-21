<?php

namespace App\Helper;

use Overtrue\Wechat\Notice;
use Overtrue\Wechat\User as WxUser;

class WXNotice
{
    // Notice对象
    private $notice;

    private $appId;
    private $secret;

    public function __construct()
    {
        $this->notice = new Notice($this->appId, $this->secret);
        $this->appId = config('wx.appId');
        $this->secret = config('wx.appSecret');
    }

    // 需求提交成功通知
    public function buyerRequestSuccessNotice($openId, $orderNumber, $orderDate)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'D89mCoiu89ltHZXixdlR_5Ey6Cj6gzTbbN8O3By_Bfg';
        $url = 'http://www.yeyetech.net/app/wx?#/buyer/orders/waitOffer';
        $data = array(
            "first"    => "您已成功提交代购需求。客服将在24小时内反馈价格，请留意电话通知 。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $orderDate."\n",
            "remark"   => "点击查看需求详情",
        );

        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 订单取消通知
    public function orderCanceled($openId, $orderNumber, $itemTitles, $orderPrice)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'v-U_Gkj1dvvlYOkJodTY8WgIRsHrpgad6Su9OAIn174';
        $data = array(
            "first"    => "您的订单已取消。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles,
            "keyword3"   => '￥'. $orderPrice,
        );

        $messageId = $this->notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 订单取消通知
    public function requestCanceled($openId, $orderNumber, $itemTitles, $orderPrice)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'v-U_Gkj1dvvlYOkJodTY8WgIRsHrpgad6Su9OAIn174';
        $data = array(
            "first"    => "您的需求已取消。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles,
            "keyword3"   => $orderPrice,
        );

        $messageId = $this->notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 加小星星
    public function addStars($openId, $friendNickName, $startAmount, $total)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'TXc4OkNNekkvRMzu-S4gqQqqhZjrVK3h1YAAXbOURZw';
        $url = "http://www.yeyetech.net/accessGoldActivity";
        $data = array(
            "first"    => "您的朋友已成功给您打赏小星星。",
            "FieldName" => "朋友昵称",
            "Account" => $friendNickName,
            "change"   => "获得",
            "CreditChange"   => $startAmount,
            "CreditTotal" => $total."\n",
            "Remark" => "点此兑换奖品"
        );

        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 超时未付款
    public function timeOut($openId, $orderPrice, $itemTitles, $buyerAddress ,$orderNumber)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = '6Ti_qyUpRmHsXGVDFg6hJUuVrCM60OTNvjV2FOWQCnw';
        $data = array(
            "first"    => "您的订单由于超时未付款已失效。",
            "orderProductPrice" => '￥'.$orderPrice,
            "orderProductName" => $itemTitles,
            "orderAddress" => $buyerAddress,
            "orderName"   => $orderNumber,
        );
        $messageId = $this->notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 付款成功
    public function paySuccess($openId, $orderNumber, $title, $payTime, $orderPrice, $days)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'KQTXg2uLsgJGVO3Vjvpoi2bUMLn2a4AP3ylPdnHFEI4';
        $url = "http://www.yeyetech.net/app/wx?#/buyer/orders/waitDelivery";
        $data = array(
            "first"    => "您已成功付款，买手将在{$days}日内发货。\n",
            "keyword1" => '￥'.$orderPrice,
            "keyword2" => $title,
            "keyword3" => '微信安全支付',
            "keyword4" => $orderNumber,
            "keyword5" => $payTime . "\n",
            "remark" => "点击查看订单详情"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 发货通知
    public function deliverItems($openId, $orderNumber, $itemTitles, $orderPrice, $orderId)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'aJQbah_dzKA9PHhBv8G2zb6pzqtOPJeP_txkhmWFjXM';
        $url = "http://www.yeyetech.net/app/wx?#/buyer/orders/delivered/detail/" . $orderId . "/logistics/" . $orderId;
        $data = array(
            "first"    => "您代购的商品已发货。直邮包裹通常10-25天到货，请耐心等待。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles,
            "keyword3" => '￥'.$orderPrice."\n",
            "remark" => "点击查物流"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 退款通知
    public function refundNotify($openId, $refundPrice, $itemTitles, $orderNumber, $reasons)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = '0WkK0Vz30l6LG_ibHxQBNNoGVIcyeDCNFyPiOJzHCVg';
        $data = array(
            "first" => "您的退款请求已被受理。",
            "keynote1" => $refundPrice,
            "keynote2" => "原支付方式退回",
            "keynote3" => "5个工作日内",
            "keynote4" => $itemTitles,
            "keynote5" => $orderNumber,
            "keynote6" => $reasons,
        );
        $messageId = $this->notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 买家报价付款
    public function tellBuyerPay($openId, $orderNumber, $itemTitles, $orderPrice, $notifyTime, $orderId)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'J02FMvqRD7aigbxrVJ70jPDCNUjWElp_xnmumFO9dV0';
        $url = "http://www.yeyetech.net/app/wx?#/buyer/orders/waitPay/pay/" . $orderId;
        $data = array(
            "first"    => "您的代购商品确认有货，请在48小时内付款。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles,
            "keyword3" => '￥'.$orderPrice,
            "keyword4" => $notifyTime."\n",
            "remark" => "点击去付款"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }


    // 卖家接单通知
    public function sellerReplyRequest($openId, $orderNumber, $replyTime)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'FBQC-KUCZ1QhBti0h3JziREKda6V1v789nhEa8NxSZ8';
        $url = "http://www.yeyetech.net/seller/management";
        $data = array(
            "first"    => "您接到一笔订单，请在买家付款后采购并发货。若无法购买，请立即联系客服。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $replyTime."\n",
            "remark" => "点击进入买手中心"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 订单取消通知，卖家
    public function orderCanceledSeller($openId, $orderNumber, $itemTitles, $orderPrice)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'i7adl5Neo2iJXJdVwv-dFDukln4XlnyPz-E1-11Gn8U';
        $data = array(
            "first"    => "抱歉，该笔订单已被买家取消。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles,
            "keyword3"   => '￥'.$orderPrice,
        );

        $messageId = $this->notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 卖家，请发货
    public function tellSellerDeliver($openId, $orderNumber, $itemTitles, $orderPrice)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'aJQbah_dzKA9PHhBv8G2zb6pzqtOPJeP_txkhmWFjXM';
        $url = "http://www.yeyetech.net/seller/management#toDeliver";
        $data = array(
            "first"    => "您承接的订单买家已付款，请于7日内发货并填写物流信息。若无法发货，请立即联系客服。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles,
            "keyword3" => '￥'.$orderPrice."\n",
            "remark" => "点击进入买手中心"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 卖家发货审核通过
    public function sellerCanWithdraw($openId, $itemTitles, $orderNumber)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'Hc4S5ItthSRavuOVqPbcMxi-7UXbT7qLVBv6TcOnBWE';
        $url = "http://www.yeyetech.net/seller/management#toWithdraw";
        $data = array(
            "first"    => "您的发货审核已通过，请提现。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles."\n",
            "remark" => "点击查看收入/提现"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 卖家发货审核不通过
    public function sellerCannotWithdraw($openId, $itemTitles, $orderNumber)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'Hc4S5ItthSRavuOVqPbcMxi-7UXbT7qLVBv6TcOnBWE';
        $url = "http://www.yeyetech.net/seller/management#toDeliver";
        $data = array(
            "first"    => "您的发货审核未通过，请填写有效的物流单号。\n",
            "keyword1" => $orderNumber,
            "keyword2" => $itemTitles."\n",
            "remark" => "点击进入买手后台"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 退款通知——卖家
    public function notifySellerRefund($openId, $refundNumber, $refundPrice)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'BMQqeU2GKEhjF74Tdw5gA0umRont-yHU2WzBEjXyqhE';
        $data = array(
            "first"    => "已退款给买家，请悉知。",
            "reason" => "协商退款 (订单尾号: ".$refundNumber.')',
            "refund" => '￥'.$refundPrice."\n",
        );
        $messageId = $this->notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 提现提交通知——卖家
    public function notifySellerWithDrawRequest($openId, $withDrawPrice, $withDrawTime)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'X8l3FVa5_lW12jCNJnNri3iSPm2pYE-rCMGZYRjr8v0';
        $url = "http://www.yeyetech.net/seller/management#toWithdraw";
        $data = array(
            "first"    => "您的提现申请已被受理，款项将在3个工作日内到帐。",
            "money" => '￥'.$withDrawPrice,
            "timet" => $withDrawTime."\n",
            "remark" => "点击查看收入/提现"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 提现成功通知——卖家
    public function notifySellerWithDrawSuccess($openId, $withDrawPrice, $withDrawSuccessTime)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = '98804GmyGHlS3yJWGI1crhSZhPZ7bjO_j_zd8UM5xmA';
        $url = "http://www.yeyetech.net/seller/management#toWithdraw";
        $data = array(
            "first"    => "提现成功，请注意查收。",
            "money" => '￥'.$withDrawPrice,
            "timet" => $withDrawSuccessTime."\n",
            "remark" => "点击查看收入/提现"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    // 提醒通知
    public function notifyBuyerActivityWillStarted($openId, $prefix, $title, $time)
    {
        if (!$this->checkUser($openId)) {
            return false;
        }
        $templateId = 'OGwUlwrcmL-u-o4-GU7ydH1fHNiS3UsytVGi_fVRcIM';
        $url = "http://www.yeyetech.net/app/wx?#/periodActivity";
        $data = array(
            "first"    => $prefix,
            "keyword1" => $title,
            "keyword2" => $time."\n",
            "remark" => "每天10:00，优惠团购准时更新。点此开抢！"
        );
        $messageId = $this->notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openId)->send();
        return $messageId;
    }

    /**
     * @param $openId
     * @return bool
     */
    private function checkUser($openId) {
        $userService = new WxUser($this->appId, $this->secret);
        $user_info = $userService->get($openId);
        if ($user_info->subscribe == 1) {
            return true;
        }
        else {
            return false;
        }
    }

}