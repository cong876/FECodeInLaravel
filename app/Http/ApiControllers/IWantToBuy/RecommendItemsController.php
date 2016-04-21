<?php

namespace App\Http\ApiControllers\IWantToBuy;

use App\Http\ApiControllers\Controller;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Transforms\RecommendGroupItemTransformer;

class RecommendItemsController extends Controller
{
    private $fetchCount = 4;
    private $activity;

    function __construct(ActivityRepositoryInterface $activity)
    {
        $this->activity = $activity;
    }

    public function getItemsInfo()
    {
        $activity =  $this->activity->getCurrentPeriodActivity();
        $groupItems =  $activity->items;
        foreach($groupItems as $item) {
            $meta = json_decode($item->attributes);
            $order_info = $meta->activity_meta;
            $item['market_price'] = $order_info->market_price;
            $item['price'] = sprintf('%.2f',($item['price'] + $order_info->postage));
        }
        $randItems =  $groupItems->shuffle()->slice(0, $this->fetchCount);
        return $this->response->collection($randItems, new RecommendGroupItemTransformer);
    }


}