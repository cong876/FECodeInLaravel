<?php
/**
 * Created by PhpStorm.
 * User: caolixiang
 * Date: 15/6/18
 * Time: 下午6:00
 */

namespace App\Repositories\Sku;


use App\Models\Sku;
use App\Repositories\BaseRepository;

class SkuRepository extends BaseRepository implements SkuRepositoryInterface{

    protected $model;

    /**
     * @param Sku $model
     */
    function __construct(Sku $model)
    {
        $this->model = $model;
    }

    /**
     * 新建一个SKU
     *
     * @param array $data
     * @param bool $isAvailable=true
     * @return \App\Models\Sku|null
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * 更新指定的SKU
     *
     * @param \App\Models\Sku $sku
     * @param array $data
     * @param bool $isAvailable=true
     * @return bool|int
     */
    public function updateSku(Sku $sku, array $data)
    {
        return $sku->update($data);
    }


    /**
     * 更新库存
     *
     * @param \App\Models\Sku $sku
     * @param int $count=-1
     * @return bool
     */
    public function modifySkuInventoryByTimeStamp(Sku $sku, $count=-1)
    {
        return $sku->modifySkuInventoryByTimeStamp($count);
    }
}