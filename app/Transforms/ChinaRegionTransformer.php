<?php

namespace App\Transforms;

use League\Fractal\TransformerAbstract;


class ChinaRegionTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $regionTransformed = [
            'code' => $model->code,
            'name' => $model->name
        ];

        if ($model->level == 3) {
            $regionTransformed['zip_code'] = $model->zip_code;
        }

        return $regionTransformed;
    }

}