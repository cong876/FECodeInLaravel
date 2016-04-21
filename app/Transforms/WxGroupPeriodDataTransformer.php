<?php


namespace App\Transforms;

use App\Models\Activity;
use App\Models\Item;
use App\Models\ItemTag;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\Cache;

class WxGroupPeriodDataTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $activity_info = json_decode($model->activity_info);
        $forward_info = json_decode($model->forward_info);

        // 主题活动头图
        $carousel_urls = $activity_info ? $activity_info->pic_url : [];
        $carousel_links = $activity_info ? $activity_info->url : [];

        $themeActivities = [];

        // Todo Fuck array structure
        for ($i = 0; $i < count($carousel_urls); $i++) {
            $activity_id = explode('activity/', $carousel_links[$i])[1];
            array_push($themeActivities, [
                'id' => $activity_id,
                'activity_title' => Activity::find($activity_id)->activity_title,
                'picture' => $carousel_urls[$i]
            ]);
        }

        $forward_title = $forward_info ? $forward_info->forward_title : '';
        $forward_description = $forward_info ? $forward_info->forward_description : '';
        $forward_pic_url = $forward_info ? $forward_info->forward_pic_url : '';

        // 今日商品详情
        $details = $this->getItemsInfo($model);

        $tomorrow = Activity::tomorrowPeriod()->first();
        $nextDetails = $this->getItemsInfo($tomorrow);
        $package =  [
            'activity_id' => $model->activity_id,
            'activity_title' => $model->activity_title,
            'details' => $details,
            'share' => [
                'title' => $forward_title,
                'description' => $forward_description,
                'pic_url' => $forward_pic_url
            ],
            'themeActivities' => $themeActivities,
            'serverTime' => date('Y-m-d H:i:s'),
            'deadline' => $model->activity_due_time,
            'activityImage' => $model->pic_urls,
            'activityType' => $model->activity_type,
            // 明日
            'nextPeriodImage' => $tomorrow->pic_urls,
            'nextDetails' => $nextDetails,
            'nextActivityType' => $tomorrow->activity_type,
            'next_activity_id' => $tomorrow->activity_id
        ];

        $expiresAt = Carbon::now()->addHours(12);
        Cache::put('PeriodActivity:' . $model->activity_id, $package, $expiresAt);
        return $package;
    }

    private function getItemsInfo($activity)
    {
        $itemDetails = [];
        $ordered_items_id = json_decode($activity->item_order);
        $ordered_items = [];
        foreach ($ordered_items_id as $item_id) {
            array_push($ordered_items, Item::find($item_id));
        }
        foreach ($ordered_items as $item) {
            // 商品元数据
            if ($item->is_on_shelf) {
                // Todo 秒杀逻辑
                $meta = json_decode($item->attributes);
                $order_info = $meta->activity_meta;
                $tags = [];
                if(!empty($meta->tag_meta)) {
                    $tag_ids = $meta->tag_meta;
                    foreach ($tag_ids as $tag_id) {
                        $tag = ItemTag::find($tag_id);
                        array_push($tags, [
                            'id' => $tag->item_tag_id,
                            'tag_name' => $tag->tag_name,
                            'style' => json_decode($tag->tag_attributes)->style,
                        ]);
                    }
                }
                array_push($itemDetails, [
                    'id' => $item->item_id,
                    'title' => $item->title,
                    'description' => $item->detail_passive->description,
                    'price' => $item->price + $order_info->postage,
                    'market_price' => $order_info->market_price,
                    'pic_url' => $item->pic_urls[0],
                    'buy_per_user' => $item->buy_per_user,
                    'tags' => $tags
                ]);
            }
        }
        return $itemDetails;
    }
}