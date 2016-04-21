<?php
/**
 * Created by PhpStorm.
 * User: ma0722
 * Date: 2015/6/19
 * Time: 11:23
 */

namespace App\Repositories\DetailPositiveExtra;

use App\Models\DetailPositiveExtra;
use App\Repositories\BaseRepository;

class DetailPositiveExtraRepository extends BaseRepository implements DetailPositiveExtraRepositoryInterface{

    protected  $model;

    /*
     * @param DetailPositiveExtra $model
     */
    public function __construct(DetailPositiveExtra $model){
        $this->model = $model;
    }

    /**
     * 创建一个我要买商品详情
     *
     * @param array $data
     * @return \App\Models\DetailPositiveExtra|null
     */
    public function create(array $data){

        $this->model->create($data);
    }

    /**
     * 更新我要买商品信息
     *
     * @param DetailPositiveExtra $detailPositiveExtra
     * @param array $data
     * @return bool|int
     */
    public function updateDetailExtraPositive(DetailPositiveExtra $detailPositiveExtra, array $data)
    {
        return $detailPositiveExtra->update($data);
    }
}