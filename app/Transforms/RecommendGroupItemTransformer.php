<?php

namespace App\Transforms;


use League\Fractal\TransformerAbstract;

class RecommendGroupItemTransformer extends TransformerAbstract
{

    public function transform($model)
    {
        return [
            'id' => (int)$model->item_id,
            'title' => $model->title,
            'marketPrice' => $model->market_price,
            'description' => $model->detail_passive->description,
            'pic_url' => $model->pic_urls[0],
            'price' => $model->price
        ];
    }
}