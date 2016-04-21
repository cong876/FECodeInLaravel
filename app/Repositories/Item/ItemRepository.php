<?php

namespace App\Repositories\Item;


use App\Models\Item;
use App\Repositories\BaseRepository;
use App\Models\Sku;
use App\Models\DetailPassiveExtra;
use App\Models\DetailPositiveExtra;


class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{

    protected $model;

    public function __construct(Item $model)
    {
        $this->model = $model;
    }

    /*
     * 判断商品是否有效
     *
     * @param Item $item
     * @return bool
     */
    public function isAvailable(Item $item)
    {
        return $item->is_available;
    }


    /**
     * @param array $data
     * @param $publisher_id
     * @param null $attributes
     * @param bool $is_positive
     * @param array|null $skus
     * @param array|null $dpo
     * @param array|null $dpa
     * @return static
     */
    public function create(array $data, $publisher_id, $attributes=null,
                           $is_positive=true , array $skus=null,
                           array $dpo=null, array $dpa=null)
    {
        $data['publisher_id'] = $publisher_id;
        $data['is_positive'] = $is_positive;
        if ($attributes) {
            $data['attributes'] = $attributes;
        }
        $item = $this->model->create($data);
        if (!empty($skus)) {
            foreach($skus as $sku) {
                $item->skus()->create($sku);
            }
        }
        if ($is_positive) {
            if(!empty($dpo)) {
                $item->detail_positive()->create($dpo);
            }
        }else {
            if(!empty($dpa)) {
                $item->detail_passive()->create($dpa);
            }
        }
        return $item;
    }

    /**
     * 更新一个商品
     *
     * @param Item $item
     * @param array $data
     * @param null $attributes
     * @param array $skus=null
     * @param array $dpo=null
     * @param array $dpa=null
     * @return bool|int
     */
    public function updateItem(Item $item, array $data, $attributes=null,
                               array $skus=null, array $dpo=null,
                               array $dpa=null)
    {
        if ($attributes) {
            $data['attributes'] = $attributes;
        }
        if (!empty($skus)) {
            $item->skus()->delete();
            foreach($skus  as $sku) {
                $item->skus()->create($sku);
            }
        }
        if (!empty($dpo)) {
            $item->detail_positive()->update($dpo);
        }
        if (!empty($dpa)) {
            $item->detail_passive()->update($dpa);
        }
        return $item->update($data);
    }

    /*
     * 删除一个Item，并置Item、SKU状态为无效
     *
     * @param App/Models/Item $item
     * @return bool
     */
    public function deleteItem(Item $item)
    {
        $item->setItemUnavailable();
        return $item->delete();
    }

    /*
     * 删除或者更新商品的类目
     *
     * @param Item $item
     * @param int $categoryId=null
     * @return bool
     */
    public function changeOrRemoveCategory(Item $item, $categoryId = null)
    {
        if (empty($categoryId)) {
            $item->category_id = null;
            return $item->save();
        } else {
            $item->category_id = $categoryId;
            return $item->save();
        }
    }

    /*
     * 增加/更新Item的Attributes
     *
     * @param array $data
     * @param App/Models/Item $item
     * @return bool
     */
    public function addOrUpdateAttributes(Item $item, array $data)
    {
        $attributes = $item->attributes;
        if (!$attributes) {
            $attributes = array();
        }
        $item->attributes = array_merge($attributes, $data);
        $item->save();
    }

    /*
     * 取得Item的属性
     *
     * @param App/Models/Item $item
     * @return array
     */
    public function getAttributes(Item $item)
    {
        return $attributes = $item->attributes;
    }

    public function getFullDetail(Item $item)
    {
        return $item->getFullDetail();
    }

}