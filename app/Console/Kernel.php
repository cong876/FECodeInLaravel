<?php

namespace App\Console;

use App\Models\Buyer;
use App\Models\GroupItem;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\SubOrder;
use Illuminate\Support\Facades\Cache;
use App\Models\Seller;
use App\Helper\WXNotice;
use Overtrue\Wechat\Staff;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Inspire',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 关闭未付款订单定时任务
        $schedule->call(
            function () {
                // 关闭订单,回补库存

                $suborders = SubOrder::canPay()->get();
                foreach ($suborders as $suborder) {
                    // 团购半小时,帮我代购48小时
                    $created_offer_time = $suborder->created_offer_time;
                    switch ($suborder->order_type) {
                        case 0:
                            $shouldClose = Carbon::now() > $created_offer_time->addHours(48) ? true : false;
                            break;
                        case 1:
                        case 3:
                            $shouldClose = Carbon::now() > $created_offer_time->addHours(6) ? true : false;
                            break;
                        case 4:
                            $shouldClose = Carbon::now() > $created_offer_time->addMinutes(10) ? true : false;
                            break;
                        default:
                            $shouldClose = false;
                    }
                    if ($shouldClose) {
                        $user = $suborder->buyer;
                        DB::beginTransaction();
                        $suborderDeleted = $suborder->update(array(
                            "sub_order_state" => 441,
                            "is_available" => false
                        ));
                        $skuUpdated = true;
                        $itemSaved = true;
                        $title = '';
                        $price = 0;
                        foreach ($suborder->items as $item) {
                            $title .= $item->title . '；';
                            $item_count = $item->item_type == 1 ? $item->detail_positive->number :
                                GroupItem::where('sub_order_id', $suborder->sub_order_id)->first()->number;
                            $price = $item->price * $item_count + $suborder->postage;

                            // 使商品无效
                            if ($suborder->order_type == 0) {
                                $item->is_available = 0;
                                $itemSaved = $item->save();
                            }
                            if ($suborder->order_type == 1 || $suborder->order_type == 3 || $suborder->order_type == 4) {
                                $sku = $item->skus->first();
                                $sku->sku_inventory += $item_count;
                                $skuUpdated = $sku->save();
                            }
                        }
                        $extras['title'] = $suborder->items->implode('title', ';');
                        if ($suborderDeleted && $skuUpdated && $itemSaved) {
                            DB::commit();
                            $notice = new WXNotice();
                            if ($user->is_subscribed == 1) {
                                if (mb_strlen($title, 'utf-8') > 12) {
                                    $title = mb_substr($title, 0, 12) . '...';
                                }
                                $notice->timeOut($user->openid, sprintf('%.2f', $price), $title, '未填写', $suborder->sub_order_number);
                            }

                        } else {
                            DB::rollback();
                        }
                    }
                }
            })->everyFiveMinutes();

        // 确认收货定时任务
        $schedule->call(function () {
            $subs = SubOrder::where('sub_order_state', '601')->get();
            foreach ($subs as $sub) {

                $query = null;

                // 先查看二段物流
                if ($second = Cache::get('suborder:' . $sub->sub_order_id . ':additional')) {
                    $second = unserialize($second);
                    $info = $second['delivery_related_url'];
                    $split = explode('?', $info);

                    if (count($split) == 2) {
                        $query = $split[1];
                    }
                } else {
                    // 国外直邮，不经过红领巾
                    if (!(Cache::get("suborder:" . $sub->sub_order_id . ":secondaryDeliver"))) {
                        $info = $sub->deliveryInfo->delivery_related_url;
                        $split = explode('?', $info);
                        $query = null;
                        if (count($split) == 2) {
                            $query = $split[1];
                        }
                    }
                }
                if ($query) {
                    sleep(20);
                    $result = @file_get_contents('http://hljmeat3.duapp.com/gocha.php?' . $query);
                    $obj = json_decode($result);
                    $state = isset($obj->state) ? $obj->state : null;
                    if ($obj && $state && ($state == 3)) {
                        if (!(Cache::get("suborder:" . $sub->sub_order_id . ":secondaryDeliver"))) {
                            $sub->sub_order_state = 301;
                            $sub->completed_time = date('Y-m-d H:i:s');
                            $sub->save();
                            $buyer = Buyer::where('hlj_id', $sub->mainOrder->hlj_id)->first();
                            $buyer->buyer_success_orders_num += 1;
                            $buyer->buyer_actual_paid += $sub->sub_order_price - $sub->refund_price;
                            $buyer->save();
                        }
                    }
                }
            }
        })->dailyAt('14:30');

        // 审核物流定时任务
        $schedule->call(function () {
            // 获取所有待审核状态的订单
            $subs = SubOrder::where('sub_order_state', '521')->get();
            foreach ($subs as $sub) {
                $info = null;
                $query = null;
                // 优先查看二段物流
                if ($twoPhaseDelivery = Cache::get('suborder:' . $sub->sub_order_id . ':additional')) {
                    $twoPhaseDelivery = unserialize($twoPhaseDelivery);
                    $info = $twoPhaseDelivery['delivery_related_url'];
                    $split = explode('?', $info);
                    if (count($split) == 2) {
                        $query = $split[1];
                    }
                } else {
                    $info = $sub->deliveryInfo->delivery_related_url;
                    $split = explode('?', $info);
                    $query = null;
                    if (count($split) == 2) {
                        $query = $split[1];
                    }
                }
                if ($query) {
                    sleep(20);
                    $result = @file_get_contents('http://hljmeat4.duapp.com/gocha.php?' . $query);
                    $obj = json_decode($result);
                    $state = isset($obj->state) ? $obj->state : null;
                    $data = isset($obj->data) ? $obj->data : null;
                    if ($data && $state >= 0) {
                        $items = $sub->items;
                        $title = '';
                        foreach ($items as $item) {
                            $title .= $item->title . '；';
                        }
                        $title = rtrim($title, '；');
                        if (mb_strlen($title) > 12) {
                            $title_notice = mb_substr($title, 0, 12) . '...';
                        } else {
                            $title_notice = $title;
                        }
                        $seller_id = $sub->seller_id;
                        $seller = Seller::find($seller_id);
                        $seller->seller_success_orders_num += 1;
                        $seller->seller_success_incoming += $sub->sub_order_price - $sub->refund_price;
                        $seller_openid = $sub->seller->user->openid;
                        $sub->sub_order_state = 601;
                        $sub->transfer_price = $sub->sub_order_price - $sub->refund_price;
                        $sub->audit_passed_time = date('Y-m-d H:i:s');
                        $sub->save();
                        $seller->save();
                        $notice = new WXNotice();
                        $notice->sellerCanWithdraw($seller_openid, $title_notice, $sub->sub_order_number);
                    }
                }
            }
        })->twiceDaily(11, 21);
        
    }
}
