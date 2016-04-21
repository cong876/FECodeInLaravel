<?php

namespace App\Listeners;

use App\Events\DeliveryNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helper\WXNotice;
use App\Helper\LeanCloud;
use App\Helper\SuborderTitleStringHelper;
use App\Utils\CutAndSplit\ItemsTitleSplit;

class DeliveryNotificationListener
{
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
     * @param  DeliveryNotification  $event
     * @return void
     */
    public function handle(DeliveryNotification $event)
    {
        //
        $user = $event->user;
        $suborder = $event->suborder;
        $items = $event->items;
        LeanCloud::sellerDeliverItemsSMS($user->mobile, ItemsTitleSplit::splitTitle($items),$suborder->operator->user->mobile);
        $notice = new WXNotice();
        $notice->deliverItems($user->openid, $suborder->sub_order_number,
            SuborderTitleStringHelper::getTitleStringAtLength($suborder,12),
            $suborder->sub_order_price - $suborder->refund_price);
    }
}
