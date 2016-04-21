<?php


namespace App\Transforms;

use League\Fractal\TransformerAbstract;
use App\Helper\ChinaRegionsHelper;


class ReceivingAddressTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        $regionInstance = ChinaRegionsHelper::getInstance();
        return [
            'id' => $model->receiving_addresses_id,
            'receiver_name' => $model->receiver_name,
            'receiver_mobile' => $model->receiver_mobile,
            'street_address' => $model->street_address,
            'is_default' => $model->is_default,
            'province' => [
                'code' => $model->first_class_area,
                'name' => $regionInstance->getRegionByCode($model->first_class_area)->name
            ],
            'city' => [
                'code' => $model->second_class_area,
                'name' => $regionInstance->getRegionByCode($model->second_class_area)->name
            ],
            'county' => [
                'code' => $model->third_class_area,
                'name' => $regionInstance->getRegionByCode($model->third_class_area)->name
            ],
            'zip_code' => $model->receiver_zip_code
        ];
    }
}