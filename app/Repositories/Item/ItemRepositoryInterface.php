<?php namespace App\Repositories\Item;

use App\Models\DetailPassiveExtra;
use App\Models\DetailPositiveExtra;
use App\Models\Item;
use App\Models\Sku;

/**
 * Interface ItemRepositoryInterface
 * @package App\Repositories\Item;
 * This interface defines the function about Item.
 */
interface ItemRepositoryInterface
{

    public function isAvailable(Item $item);

    public function create(array $data, $publisher_id, $attributes=null,
                           $is_positive=true ,array $sku=null,
                           array $dpo=null, array $dpa=null);

    public function updateItem(Item $item, array $data, $attributes=null,
                               array $sku=null, array $dpo=null,
                               array $dpa=null);

    public function deleteItem(Item $item);

    public function changeOrRemoveCategory(Item $item);

    public function addOrUpdateAttributes(Item $item, array $data);

    public function getAttributes(Item $item);

    public function getFullDetail(Item $item);


}