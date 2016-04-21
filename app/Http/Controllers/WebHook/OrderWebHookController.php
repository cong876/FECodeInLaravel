<?php

namespace App\Http\Controllers\WebHook;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\OrderRefund;
use App\Models\SubOrder;
use Illuminate\Support\Facades\Input;
use App\Models\User;
use App\Helper\ChinaRegionsHelper;
use App\Helper\WXNotice;
use App\Events\MailToSellerForDeliverEvent;
use App\Helper\LeanCloud;


class OrderWebHookController extends Controller
{

    // 接收是否付款的WebHook
    function verifyPay()
    {
        $regionInstance = ChinaRegionsHelper::getInstance();
        $event = Input::all();
        if (!isset($event['type'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        if ($event['type'] == "charge.succeeded") {
            $dataObject = $event['data']['object'];
            $order_no = $dataObject['order_no'];
            $sub = SubOrder::where('sub_order_number', $order_no)->first();
            $ori_state = $sub->sub_order_state;
            if ($sub && $dataObject['paid']) {
                $sub->charge_id = $dataObject['id'];
                if ($ori_state == 241) {
                    $sub->sub_order_state = 541;
                } else {
                    $sub->sub_order_state = 501;
                }
                $sub['ppp_status'] = true;
                if(!$sub->payment_time) {
                    $sub->payment_time = date('Y-m-d H:i:s');
                }

                // 增加团购,秒杀销量数量
                if ($sub->order_type == 1 || $sub->order_type == 4) {

                    // 每人限购
                    $item = $sub->items->first();

                    // 当前用户购买件数
                    $group_item = $sub->groupItems->first();
                    $current_user_buy_number = $group_item->number;

                    $item->sold_count += $current_user_buy_number;
                    $item->save();

                }

                $sub->save();

                // 增加购买次数等
                $buyer = $sub->mainOrder->user->buyer;
                $buyer->buyer_paid_count += 1;
                $buyer->buyer_initial_paid += $sub->sub_order_price;
                $buyer->save();

                // 发送邮件模板等
                $items = $sub->items;
                $title = '';
                $title_mail = '';

                if (count($items) > 1) {
                    $title_mail = $items[0]->title . '...';
                } else {
                    $title_mail = $items[0]->title;
                }
                foreach ($items as $item) {
                    $title .= $item->title . '；';
                }
                $email = $sub->seller->user->email;
                $hlj_id = $sub->mainOrder->hlj_id;
                $buyer_openid = User::find($hlj_id)->openid;
                $title = rtrim($title, '；');
                if (mb_strlen($title) > 12) {
                    $title_notice = mb_substr($title, 0, 12) . '...';
                } else {
                    $title_notice = $title;
                }

                $receiver_name = $sub->receivingAddress->receiver_name;
                $receiver_mobile = $sub->receivingAddress->receiver_mobile;
                $receiver_zip_code = $sub->receivingAddress->receiver_zip_code;
                $province_code = $sub->receivingAddress->first_class_area;
                $city_code = $sub->receivingAddress->second_class_area;
                $county_code = $sub->receivingAddress->third_class_area;
                $street_address = $sub->receivingAddress->street_address;
                $province_level = $regionInstance->getRegionByCode($province_code)->name;
                $city_level = $regionInstance->getRegionByCode($city_code)->name;
                if ($county_code == 1) {
                    $county_level = "";
                } else {
                    $county_level = $regionInstance->getRegionByCode($county_code)->name;
                }
                $receiving_address = $province_level . $city_level . $county_level . $street_address;


                // 是否待报价拒单
                $notice = new WXNotice();
                if ($ori_state == 201) {
                    $notice->tellSellerDeliver($sub->seller->user->openid, $sub->sub_order_number,
                        $title_notice, sprintf('%.2f', $sub->sub_order_price));
                    // 发送卖家发货提醒
                    \Event::fire(new MailToSellerForDeliverEvent($email, $sub->sub_order_number,
                        $title_mail, sprintf('%.2f', $sub->sub_order_price), $sub->payment_time,
                        $receiver_name, $receiver_mobile, $receiving_address, $receiver_zip_code, time(), 1));
                }
                // 推送微信模板

                if ($sub->country_id == 4) {
                    $days = 10;
                } else {
                    $days = 7;
                }
                $notice->paySuccess($buyer_openid, $sub->sub_order_number, $title_notice, $sub->payment_time, sprintf('%.2f', $sub->sub_order_price), $days);


                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
            } elseif (!$sub && $dataObject['paid']) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK'); // 测试环境
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            }

        }
    }

    // 接收是否退款的WebHook
    function verifyRefund()
    {
        $event = Input::all();
        if (!isset($event['type'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        if ($event['type'] == "refund.succeeded") {
            $dataObject = $event['data']['object'];
            $order_id = $dataObject['id'];
            $target = OrderRefund::where('ppp_refund_order_id', $order_id)->first();
            if ($target && $dataObject['succeed']) {
                $target->refund_success_time = date('Y-m-d H:i:s', $dataObject['time_succeed']);
                $target->ppp_status = $dataObject['status'];
                $target->is_successful = $dataObject['succeed'];
                $target->save();
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
            } elseif (!$target && $dataObject['succeed']) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK'); // 测试环境
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            }

        }
    }

}
