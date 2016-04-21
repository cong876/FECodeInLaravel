<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/8/11
 * Time: 上午10:17
 */
namespace App\Repositories\DeliveryInfo;

use App\Models\DeliveryInfo;

interface DeliveryInfoRepositoryInterface
{
    public function createDelivery(array $data);
}