<?php


namespace App\Transforms;

use App\Models\GroupItem;
use League\Fractal\TransformerAbstract;


class MyOrderWxAfterPaidSubOrderTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $extras = $this->getExtraInformation($model);
        if (empty($model->snapshot->paid_snapshot)) {
            return [];
        }
        $snapshot = json_decode($model->snapshot->paid_snapshot);
        if ($model->order_type = 1 || $model->order_type = 4) {
            $gi = GroupItem::where('sub_order_id', $model->sub_order_id ?? '')->first();
            $memo = $gi->memo ?? '';
        } else {
            $memo = '';
        }
        return [
            'details' => $snapshot->items,
            'id' => $model->sub_order_id,
            'title' => $snapshot->title,
            'number' => $snapshot->number,
            'price' => $snapshot->price,
            'country' => $model->country->name,
            'seller' => $extras['seller'],
            'order_number' => $model->sub_order_number,
            'pic_url' => $snapshot->pic_urls ? $snapshot->pic_urls[0] : '',
            'operatorMobile' => $extras['operatorMobile'],
            'memo' => $memo,
            'address' => $snapshot->address,
            'submit_time' => $extras['submit_time'],
            'offer_time' => $extras['offer_time'],
            'payment_time' => $extras['payment_time'],
            'delivery_time' => $extras['delivery_time'],
            'complete_time' => $extras['complete_time'],
            'refunds' => $extras['refunds']
        ];
    }

    public function getExtraInformation($suborder, $extras = [])
    {

        $extras['seller'] = mb_substr($suborder->seller->real_name, 0, 1). "同学";
        $extras['country'] = $suborder->country->name;
        $extras['operatorMobile'] = $suborder->operator ? $suborder->operator->user->mobile : '18701133614';
        $extras['offer_time'] = $suborder->created_offer_time->toDateTimeString();
        if ($suborder->mainOrder->requirement) {
            $extras['submit_time'] = $suborder->mainOrder->requirement->created_at->toDateTimeString();
        } else {
            $extras['submit_time'] = $extras['offer_time'];
        }

        $extras['refunds'] = [];

        // 退款信息
        if ($refunds = $suborder->refunds) {
            foreach ($refunds as $refund) {
                if (strpos($refund->description, '###') === false) {
                    $description = $refund->description;
                } else {
                    $description = explode('###', $refund->description)[1];
                }
                array_push($extras['refunds'], [
                    'amount' => (int)($refund->refund_price * 100),
                    // Todo Refund
                    'description' => trim($description)
                ]);
            }
        }

        $extras['offer_time'] = $suborder->created_offer_time ? $suborder->created_offer_time->toDateTimeString() : '';
        $extras['payment_time'] = $suborder->payment_time ? $suborder->payment_time : '';
        $extras['delivery_time'] = $suborder->delivery_time ? $suborder->delivery_time->toDateTimeString() : '';
        $extras['complete_time'] = $suborder->completed_time ? $suborder->completed_time : '';

        return $extras;
    }

}