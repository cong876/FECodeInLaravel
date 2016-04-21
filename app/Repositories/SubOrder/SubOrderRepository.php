<?php

namespace App\Repositories\SubOrder;

use App\Models\ItemTag;
use App\Models\SubOrder;
use App\Models\SubOrderSnapshot;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use App\Helper\ChinaRegionsHelper;
use App\Models\Requirement;
use App\Models\GroupItem;

class SubOrderRepository extends BaseRepository implements SubOrderRepositoryInterface
{
    protected $model;

    public function __construct(SubOrder $model)
    {
        $this->model = $model;
    }

    /**
     * 生成子订单
     * @param array $data
     * @return static
     */
    public function createSubOrder(array $data)
    {
        $micro = microtime(true);
        $split = explode('.', $micro);
        $sub_order_number = 'YE' . implode('', $split);
        $data['sub_order_number'] = $sub_order_number;
        $suborder = $this->model->create($data);
        return $suborder;
    }

    /**
     * 更新子订单
     * @param SubOrder $subOrder
     * @param array $data
     * @return bool|int
     */
    public function updateSubOrder(SubOrder $subOrder, array $data)
    {
        return $subOrder->update($data);
    }

    /**
     * 删除子订单
     * @param SubOrder $subOrder
     * @return bool
     */
    public function deleteSubOrder(SubOrder $subOrder)
    {
        $subOrder->is_available = false;
        $subOrder->sub_order_state = 431;
        return $subOrder->save();


    }

    /**
     * 买家删除子订单
     * @param SubOrder $subOrder
     * @return bool
     */
    public function deleteSubOrderByUser(SubOrder $subOrder)
    {
        $subOrder->is_available = false;
        $subOrder->sub_order_state = 411;
        return $subOrder->save();

    }

    /**
     * 获取所有待报价订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllWaitOfferOrderWithPaginate($pageCount)
    {
        return $this->model->waitOffer()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有待付款订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllWaitPayOrderWithPaginate($pageCount)
    {
        return $this->model->waitPay()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有该运营处理的待报价订单
     * @return static
     */
    public function getAllWaitOfferOrderWithPaginateByEmployeeId()
    {
        $user = auth::user()->hlj_id;
        $requirements = Requirement::where('operator_id', $user)->where('state', 201)->get();
        $sub_order = [];
        foreach ($requirements as $requirement) {
            $subOrders = $requirement->main_order->SubOrders;
            foreach ($subOrders as $subOrder) {
                array_push($sub_order, $subOrder);
            }
        }
        return collect($sub_order)->sortByDesc('updated_at');
    }

    /**
     * 获取所有待发货订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllWaitDeliveryOrderWithPaginate($pageCount)
    {
        return $this->model->waitDelivery()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有已关闭订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllClosedOrderWithPaginate($pageCount)
    {
        return $this->model->orderClosed()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有已发货订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllHasDeliveredOrderWithPaginate($pageCount)
    {
        return $this->model->hasDelivered()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有已完成订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllHasFinishedOrderWithPaginate($pageCount)
    {
        return $this->model->hasFinished()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有审核中订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllAuditingOrderWithPaginate($pageCount)
    {
        return $this->model->WaitAuditing()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有买手拒单待分配订单
     * @param $pageCount
     * @return mixed
     */
    public function getAllSellerAssignOrderWithPaginate($pageCount)
    {
        return $this->model->SellerAssign()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }


    public function createOrUpdateSubOrderBidSnapshot(SubOrder $subOrder)
    {

        // Todo 便签快照
        $itemsArray = array();
        $itemsArray['items'] = $subOrder->items->map(function ($item) use ($subOrder) {
            $meta = json_decode($item->attributes);
            $tags = [];
            if(!empty($meta->tag_meta)) {
                $tag_ids = $meta->tag_meta;
                foreach ($tag_ids as $tag_id) {
                    $tag = ItemTag::find($tag_id);
                    array_push($tags, [
                        'id' => $tag->item_tag_id,
                        'tag_name' => $tag->tag_name,
                        'style' => json_decode($tag->tag_attributes)->style,
                    ]);
                }
            }
            return [
                'id' => $item->item_id,
                'title' => $this->deleteHtml($item->title),
                'pic_urls' => $item->pic_urls,
                'item_type' => $item->item_type,
                'number' => $item->item_type == 1 ? $item->detail_positive->number : GroupItem::where('sub_order_id', $subOrder->sub_order_id)->first()->number,
                'price' => $item->price,
                'tags' => $tags
            ];
        });

        if ($snapshot = $subOrder->snapshot) {
            $snapshot->bid_snapshot = json_encode($itemsArray);
            $snapshot->save();

        } else {
            $subOrder->snapshot()->create([
                'bid_snapshot' => json_encode($itemsArray)
            ]);
        }

    }


    public function createOrUpdateSubOrderPaidSnapshot(SubOrder $subOrder)
    {
        // Todo 便签快照
        $snapArray = array();
        $snapArray['title'] = '';
        $snapArray['pic_urls'] = [];
        $snapArray['items'] = $subOrder->items->map(function ($item) use ($subOrder, $snapArray) {
            $item_count = $item->item_type == 1 ?
                $item->detail_positive->number :
                GroupItem::where('sub_order_id', $subOrder->sub_order_id)->first()->number;
            $meta = json_decode($item->attributes);
            $tags = [];
            if(!empty($meta->tag_meta)) {
                $tag_ids = $meta->tag_meta;
                foreach ($tag_ids as $tag_id) {
                    $tag = ItemTag::find($tag_id);
                    array_push($tags, [
                        'id' => $tag->item_tag_id,
                        'tag_name' => $tag->tag_name,
                        'style' => json_decode($tag->tag_attributes)->style,
                    ]);
                }
            }
            return [
                'id' => $item->item_id,
                'title' => $item->title,
                'pic_urls' => $item->pic_urls,
                'item_type' => $item->item_type,
                'number' => $item_count,
                'price' => $item->price,
                'total_price' => $item->price * $item_count,
                'tags' => $tags
            ];
        });

        foreach ($subOrder->items as $item) {
            $snapArray['title'] .= $item->title . ';';
            $snapArray['pic_urls'] = array_merge($snapArray['pic_urls'], $item->pic_urls);
        }

        $item_collection = collect($snapArray['items']);
        $snapArray['number'] = $item_collection->sum('number');
        $snapArray['price'] = $item_collection->sum('total_price') + $subOrder->postage;

        $receivingAddress = $subOrder->receivingAddress;
        $regionInstance = ChinaRegionsHelper::getInstance();
        $snapArray['address'] = [
            'receiver_name' => $receivingAddress->receiver_name,
            'receiver_mobile' => $receivingAddress->receiver_mobile,
            'street_address' => $receivingAddress->street_address,
            'province' => [
                'code' => $receivingAddress->first_class_area,
                'name' => $regionInstance->getRegionByCode($receivingAddress->first_class_area)->name
            ],
            'city' => [
                'code' => $receivingAddress->second_class_area,
                'name' => $regionInstance->getRegionByCode($receivingAddress->second_class_area) ? $regionInstance->getRegionByCode($receivingAddress->second_class_area)->name : ''
            ],
            'county' => [
                'code' => $receivingAddress->third_class_area,
                'name' => $regionInstance->getRegionByCode($receivingAddress->third_class_area) ? $regionInstance->getRegionByCode($receivingAddress->third_class_area)->name : ''
            ],
            'zip_code' => $receivingAddress->receiver_zip_code
        ];


        if ($snapshot = $subOrder->snapshot) {
            $snapshot->paid_snapshot = json_encode($snapArray);
            $snapshot->save();
        } else {
            $subOrder->snapshot()->create([
                'paid_snapshot' => json_encode($snapArray)
            ]);
        }
    }

    public function hideFinishedSubOrder(SubOrder $subOrder)
    {
        if ($subOrder->sub_order_state == 301) {
            $subOrder->hide = true;
            return $subOrder->save();
        } else return false;
    }


    public function deleteHtml($str)
    {
        $str = trim($str);
        $str = strip_tags($str, "");
        $str = preg_replace("{\t}", "", $str);
        $str = preg_replace("{\r\n}", "", $str);
        $str = preg_replace("{\r}", "", $str);
        $str = preg_replace("{\n}", "", $str);
        return $str;
    }
}