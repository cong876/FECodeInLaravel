<?php

namespace App\Transforms;

use App\Models\Requirement;
use League\Fractal\TransformerAbstract;


class MyOrderWxRequirementTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => (int)$model->requirement_id,
            'title' => $model->title ?? '',
            'number' => $model->number ?? 0,
            'requirementNumber' => $model->requirement_number,
            'pic_url' => $model->pic_urls ? $model->pic_urls[0]: '',
            'operatorMobile' => $model->operatorMobile,
            'details' => $model->details,
            'submit_time' => $model->created_at
        ];
    }
}