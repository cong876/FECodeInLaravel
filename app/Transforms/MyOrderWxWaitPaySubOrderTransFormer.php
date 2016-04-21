<?php


namespace App\Transforms;

use App\Models\GroupItem;
use League\Fractal\TransformerAbstract;


class MyOrderWxWaitPaySubOrderTransFormer extends TransformerAbstract
{
    public function transform($model)
    {

        $extras = $this->getExtraInformation($model);
        if ($model->order_type = 1 || $model->order_type = 4) {
            $gi = GroupItem::where('sub_order_id', $model->sub_order_id)->first();
            $memo = $gi->memo ?? '';
        } else {
            $memo = '';
        }
        return [
            'id' => $model->sub_order_id,
            'title' => $extras['title'],
            'number' => $extras['number'],
            'price' => $extras['price'],
            'country' => $extras['country'],
            'order_number' => $model->sub_order_number,
            'pic_url' => count($extras['pic_urls']) > 0 ? $extras['pic_urls'][0] : '',
            'operatorMobile' => $extras['operatorMobile'],
            'memo' => $memo,
            'details' => $extras['details'],
            'submit_time' => $extras['submit_time'],
            'offer_time' => $extras['offer_time']
        ];
    }

    public function getExtraInformation($suborder, $extras = [])
    {
        $extras['details'] = [];
        $extras['pic_urls'] = [];
        if ($suborder->snapshot && $bid_snapshot = $suborder->snapshot->bid_snapshot) {

            $snap = json_decode($bid_snapshot);
            $items = $snap->items;
            foreach ($items as $item) {
                $item_count = $item->number;
                $extras['pic_urls'] = array_merge($extras['pic_urls'], $item->pic_urls);
                array_push($extras['details'], [
                    'title' => $item->title,
                    'pic_urls' => $item->pic_urls,
                    'number' => $item_count,
                    'price' => $item->price,
                    'total_price' => $item->price * $item_count
                ]);
            }
            $extras['number'] = collect($extras['details'])->sum('number');
            $extras['price'] = collect($extras['details'])->sum('total_price') + $suborder->postage;
            $extras['title'] = collect($items)->implode('title', ';');

        } else {
            $items = $suborder->items;
            if ($suborder->sub_order_state == 201 || $suborder->sub_order_state == 241) {
                foreach ($items as $item) {
                    $item_count = $item->item_type == 1 ?
                        $item->detail_positive->number :
                        GroupItem::where('sub_order_id', $suborder->sub_order_id)->first()->number;
                    $extras['pic_urls'] = array_merge($extras['pic_urls'], $item->pic_urls);
                    array_push($extras['details'], [
                        'title' => $item->title,
                        'pic_urls' => $item->pic_urls,
                        'number' => $item_count,
                        'price' => $item->price,
                        'total_price' => $item->price * $item_count
                    ]);
                }
                $extras['number'] = collect($extras['details'])->sum('number');
                $extras['price'] = collect($extras['details'])->sum('total_price') + $suborder->postage;
                $extras['title'] = $items->implode('title', ';');
            }
        }
        $extras['country'] = $suborder->country->name;
        $extras['operatorMobile'] = $suborder->operator ? $suborder->operator->user->mobile : '18701133614';
        $extras['offer_time'] = $suborder->created_offer_time ? $suborder->created_offer_time->toDateTimeString() : '';
        if ($suborder->mainOrder->requirement) {
            $extras['submit_time'] = $suborder->mainOrder->requirement->created_at->toDateTimeString();
        } else {
            $extras['submit_time'] = $extras['offer_time'];
        }
        return $extras;
    }

}