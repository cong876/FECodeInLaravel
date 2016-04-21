<?php


namespace App\Transforms;

use Cache;
use Carbon\Carbon;
use DB;
use League\Fractal\TransformerAbstract;


class SecKillTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $item = DB::table('items')->where('item_id', $model->item_id)->first();
        $meta = json_decode($item->attributes);
        $order_info = json_decode($meta)->activity_meta;
        $stated = time() > strtotime($model->start_time);
        $transformed =  [
            'id' => (int)$model->id,
            'start_time' => $model->start_time,
            'started' => $stated,
            'is_on_shelf' => boolval($item->is_on_shelf),
            'item' => [
                'item_id' => $item->item_id,
                'title' => $item->title,
                'pic_urls' => $item->pic_urls,
                'price' => sprintf("%.2f",$item->price),
                'buy_per_user' => $item->buy_per_user,
                'market_price' => sprintf("%.2f", $order_info->market_price),
                'operator_id' => $order_info->operator_id,
                'postage' => sprintf("%.2f", $order_info->postage),
            ]
        ];

        if (!$item->is_on_shelf) {
            /* 缓存单个秒杀数据结果,并标志该秒杀活动已经被缓存 */
            $cache_key = 'SecKill:' . $model->id . ':Cache';
            $cached_key = 'SecKill:' . $model->id . ':HasCached';
            $expiresAt = Carbon::now()->addHours(48);
            Cache::put($cache_key, $transformed, $expiresAt);
            Cache::put($cached_key, 1, $expiresAt);
        }
        return $transformed;
    }
}