<?php


namespace App\Transforms;

use League\Fractal\TransformerAbstract;


class WxAppUserTransformer extends  TransformerAbstract
{

    public function transform($model)
    {
        return [
            'nickname' => $model->nickname,
            'mobile' => $model->mobile,
            'email' => $model->email,
            'headImageUrl' => $model->headimgurl,
            'isNewbuyer' => $model->isNewBuyer,
            'hlj_id' => $model->hlj_id
        ];
    }
}