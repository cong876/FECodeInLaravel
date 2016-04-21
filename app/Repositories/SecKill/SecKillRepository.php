<?php

namespace App\Repositories\SecKill;

use App\Models\Seckill;
use App\Repositories\BaseRepository;
use App\Repositories\Item\ItemRepositoryInterface;
use DB;

class SecKillRepository extends BaseRepository implements SecKillRepositoryInterface
{
    protected $model;
    protected $item;

    public function __construct(Seckill $model, ItemRepositoryInterface $item)
    {
        $this->model = $model;
        $this->item = $item;
    }

    public function createSecKill(array $data, $hlj_id)
    {
        $employee = DB::table('employees')->where('hlj_id', $hlj_id)->first();
        $data['employee_id'] = $employee->employee_id;
        return $this->model->create($data);
    }

    public function createSecKillForActivity(array $data, $hlj_id, $activity_id)
    {
        $employee = DB::table('employees')->where('hlj_id', $hlj_id)->first();
        $activity = DB::table('activities')->where('activity_id', $activity_id)->first();
        $data['employee_id'] = $employee->employee_id;
        $data['activity_id'] = $activity_id;
        $data['due_time'] = $activity->activity_due_time;
        return $this->model->create($data);
    }

    public function createSecKillForActivityWithItem(array $secKillData, array $itemData,
                                                     $publisher_id, $activity_id)
    {
        $activity = DB::table('activities')->where('activity_id', $activity_id)->first();
        // 生成商品
        $meta = [];
        $order_info['market_price'] = $itemData['market_price'];
        $order_info['operator_id'] = $itemData['operator_id'];
        $order_info['postage'] = $itemData['postage'];
        $order_info['seller_id'] = $itemData['seller_id'];
        $meta['activity_meta'] = $order_info;
        DB::beginTransaction();
        $secKillItem = $this->item->create(
            array(
                'title' => $itemData['title'],
                'pic_urls' => $itemData['pic_urls'],
                'price' => $itemData['price'],
                'is_on_shelf' => false,
                'is_available' => false,
                'country_id' => $itemData['country_id'],
                'item_type' => 5,
                'buy_per_user' => 1
            ),
            $publisher_id, json_encode($meta), false, array(
            array(
                'sku_spec' => 'Normal',
                'sku_inventory' => $itemData['sku_inventory'],
                'sku_price' => $itemData['price']
            )
        ), null, array('description' => $itemData['description']));
        $secKillData['employee_id'] = $publisher_id;
        $secKillData['activity_id'] = $activity_id;
        $secKillData['due_time'] = $activity->activity_due_time;
        $secKillData['item_id'] = $secKillItem->item_id;
        $secKillData['is_available'] = 0;
        $secKill = $this->model->create($secKillData);
        if ($secKillItem && $secKill) {
            DB::commit();
            return [
                'item_id' => $secKillItem->item_id,
                'secKill_id' => $secKill->id
            ];
        } else {
            DB::rollBack();
        }
    }

    public function updateSecKillForActivityWithItem($secKill_id, array $secKillData, array $itemData,
                                                     $publisher_id, $activity_id)
    {
        $activity = DB::table('activities')->where('activity_id', $activity_id)->first();
        $meta = [];
        $order_info['market_price'] = $itemData['market_price'];
        $order_info['operator_id'] = $itemData['operator_id'];
        $order_info['postage'] = $itemData['postage'];
        $order_info['seller_id'] = $itemData['seller_id'];
        $meta['activity_meta'] = $order_info;
        DB::beginTransaction();
        $updatingItem = Seckill::find($secKill_id)->item;
        $updatedItem = $this->item->updateItem($updatingItem,
            array(
                'title' => $itemData['title'],
                'pic_urls' => $itemData['pic_urls'],
                'price' => $itemData['price'],
                'country_id' => $itemData['country_id'],
                'publisher_id' => $publisher_id
            ),
            json_encode($meta), array(
                array(
                    'sku_spec' => 'Normal',
                    'sku_inventory' => $itemData['sku_inventory'],
                    'sku_price' => $itemData['price']
                )
            ), null, array('description' => $itemData['description']));
        $secKillData['employee_id'] = $publisher_id;
        $secKillData['activity_id'] = $activity_id;
        $secKillData['due_time'] = $activity->activity_due_time;
        $secKillData['item_id'] = $updatingItem->item_id;
        $updatedSecKill = Seckill::find($secKill_id)->update($secKillData);
        if ($updatedItem && $updatedSecKill) {
            DB::commit();
            return true;
        } else {
            DB::rollBack();
            return false;
        }


    }

    public function setStatusToAvailable(Seckill $secKill)
    {
        $secKill->is_available = 1;
        $item = $secKill->item;
        $item->is_available = 1;
        return ($secKill->save() && $item->save());
    }

    public function setStatusToUnavailable(Seckill $secKill)
    {
        $secKill->is_available = 0;
        $item = $secKill->item;
        $item->is_available = 0;
        $item->is_on_shelf = 0;
        return ($secKill->save() && $item->save());
    }

    public function deleteSecKill(Seckill $secKill)
    {
        $relatedItem = $secKill->item;
        return ($relatedItem->delete() && $secKill->delete());
    }

    public function putOnShelf(Seckill $secKill)
    {
        $secKill->is_available = 1;
        $item = $secKill->item;
        $item->is_available = 1;
        $item->is_on_shelf = 1;
        return ($secKill->save() && $item->save());
    }

    public function putOffShelf(Seckill $secKill)
    {
        $item = $secKill->item;
        $item->is_available = 1;
        $item->is_on_shelf = 0;
        return ($item->save());
    }
}




