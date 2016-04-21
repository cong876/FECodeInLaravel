<?php

namespace App\Http\ApiControllers\IWantToBuy;

use App\Http\ApiControllers\Controller;
use App\Transforms\RecommendLuckyBagItemTransformer;
use App\Models\Item;

class LuckyBagItemController extends Controller {

    public function getItem()
    {
        $items = Item::where('item_type',4)->where('is_on_shelf',true)->get();
        foreach($items as $item) {
            $meta = json_decode($item->attributes);
            $order_info = $meta->activity_meta;
            $item['market_price'] = $order_info->market_price;
            $item['price'] = sprintf('%.2f',($item['price'] + $order_info->postage));
        }
        $item = $items->shuffle()->first();
        return $this->response->item($item, new RecommendLuckyBagItemTransformer);
    }
}
