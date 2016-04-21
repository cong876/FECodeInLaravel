<?php

namespace App\Repositories\OrderRefund;

use App\Models\OrderRefund;
use App\Repositories\BaseRepository;

class OrderRefundRepository extends BaseRepository implements  OrderRefundRepositoryInterface
{
    protected $model;

    function __construct (OrderRefund $model)
    {
        $this->model = $model;
    }

    /**
     *
     * 生成订单退款记录
     * @param array $data
     * @return static
     */
    public function createRefundOrder(array $data)
    {
       return $this->model->create($data);
    }
}