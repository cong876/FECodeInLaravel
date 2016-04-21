<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/8/11
 * Time: 上午10:17
 */
namespace App\Repositories\DeliveryInfo;

use App\Models\DeliveryInfo;
use App\Repositories\BaseRepository;

class DeliveryInfoRepository extends BaseRepository implements DeliveryInfoRepositoryInterface {

    protected $model;

    function __construct(DeliveryInfo $model)
    {
        $this->model = $model;
    }

    /**
     *
     * 填写新的物流信息
     * @param array $data
     * @return static
     */
    public function createDelivery(array $data)
    {
        $delivery = $this->model->create($data);
        return $delivery;
    }

}