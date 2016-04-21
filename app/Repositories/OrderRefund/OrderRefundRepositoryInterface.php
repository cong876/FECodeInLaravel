<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/8/4
 * Time: 上午9:15
 */
namespace App\Repositories\OrderRefund;

use App\Models\OrderRefund;

interface OrderRefundRepositoryInterface
{
    public function createRefundOrder(array $data);
}