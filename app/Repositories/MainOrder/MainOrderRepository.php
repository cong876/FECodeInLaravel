<?php

namespace App\Repositories\MainOrder;

use App\Models\MainOrder;
use App\Repositories\BaseRepository;

class MainOrderRepository extends BaseRepository implements MainOrderRepositoryInterface
{

    protected $model;

    public function __construct(MainOrder $model)
    {
        $this->model = $model;
    }

    /**
     * 生成逻辑主订单
     * @param array $data
     * @param $hlj_id
     * @return static
     */
    public function createMainOrder(array $data, $hlj_id)
    {
        $data ['hlj_id'] = $hlj_id;
        $mainOrder = $this->model->create($data);
        return $mainOrder;
    }

    /**
     * 更新逻辑主订单
     * @param MainOrder $mainOrder
     * @param array $data
     * @return bool|int
     */
    public function updateMainOrder(MainOrder $mainOrder, array $data)
    {
        return $mainOrder->update($data);
    }

    /**
     * 删除逻辑主订单
     * @param MainOrder $mainOrder
     * @return bool
     */
    public function deleteMainOrder(MainOrder $mainOrder)
    {
        $mainOrder->main_order_state = 431;
        return $mainOrder->save();

    }

    /**
     * 买家删除逻辑主订单
     * @param MainOrder $mainOrder
     * @return bool
     */
    public function deleteMainOrderByUser(MainOrder $mainOrder)
    {
        $mainOrder->main_order_state = 411;
        return $mainOrder->save();

    }

    /**
     * 计算逻辑主订单价格
     * @param MainOrder $mainOrder
     */
    public function getMainOrderPrice(MainOrder $mainOrder)
    {
        $main_order_price = 0;
        $postage = 0;
        $count_items = count($mainOrder->items);
        for ($i = 0; $i < $count_items; $i++) {
            $price = $mainOrder->items[$i]->skus->first()->sku_price;
            $number = $mainOrder->items[$i]->skus->first()->sku_inventory;
            $main_order_price = $main_order_price + $price * $number;
        }
        $subOrders = $mainOrder->subOrders;
        $count_subOrder = count($mainOrder->subOrders);
        for ($j = 0; $j < $count_subOrder; $j++) {
            if ($subOrders[$j]->is_available == 1) {
                $postage = $postage + $subOrders[$j]->postage;
            }

        }
        $main_order_price = $main_order_price + $postage;
        return $this->updateMainOrder($mainOrder, array('main_order_price' => $main_order_price));
    }

    /**
     * 获取所有待发送报价需求
     * @param $pageCount
     * @return mixed
     */
    public function getAllWaitSendPriceOrdersWithPaginate($pageCount)
    {
        return $this->model->with('user')->waitSendPrice()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }
}