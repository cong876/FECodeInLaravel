<?php
/**
 * Created by PhpStorm.
 * User: ma0722
 * Date: 2015/6/19
 * Time: 10:25
 */

namespace App\Repositories\DetailPassiveExtra;

use App\Models\DetailPassiveExtra;
use App\Repositories\BaseRepository;

class DetailPassiveExtraRepository extends BaseRepository implements DetailPassiveExtraRepositoryInterface{

    protected $model;

    /*
     * @param DetailPassiveExtra $model
     */
    function  __construct(DetailPassiveExtra $model){

        $this->model = $model;
    }

    /**
     * 创建一个我要发布商品详情
     *
     * @param array $data
     * @return \App\Models\DetailPassiveExtra|null
     */
    public function create(array $data){
        return $this->model->create($data);
    }

    /**
     * 更新我要发布商品详情
     *
     * @param DetailPassiveExtra $detailPassiveExtra
     * @param array $data
     * @return bool|int
     */
    public function updateDetailPassiveExtra(DetailPassiveExtra $detailPassiveExtra, array $data){
        return $detailPassiveExtra->update($data);
    }


}