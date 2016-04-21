<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/6/25
 * Time: 下午6:19
 */

namespace App\Repositories\Buyer;

use App\Models\Buyer;
use App\Repositories\BaseRepository;

class BuyerRepository extends BaseRepository implements BuyerRepositoryInterface
{

    protected $model;

    public function __construct(Buyer $model)
    {
        $this->model = $model;
    }

    /**
     *
     * 创建买家信息
     * @param array $data
     * @param $hlj_id
     * @return static
     */
    public function createBuyerInfo(array $data, $hlj_id)
    {
        $data['hlj_id'] = $hlj_id;
        return $this->model->create($data);
    }

    /**
     *
     * 展示所有买家信息
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function showAllBuyerInfo()
    {
        return $this->model->all();
    }

    /**
     *
     * 通过买家信息获取用户信息
     * @param Buyer $buyer
     * @return mixed
     */
    public function getUserInfoByBuyerInfo(Buyer $buyer)
    {
        return $buyer->user()->first();
    }

    /**
     *
     * 展示所有买家信息
     * @param $id
     * @return array
     */
    public function showBuyerInfo($id)
    {
        $str = [];
        $str['BuyerInfo'] = $this->getById($id);
        $str['email'] = $this->getById($id)->user->email;
        return $str;
    }

    /**
     *
     * 更新买家信息
     * @param Buyer $buyer
     * @param array $data
     * @return bool|int
     */
    public function updateBuyerInfo(Buyer $buyer, array $data)
    {
        return $buyer->update($data);
    }

    /**
     *
     * 删除买家信息
     * @param $id
     * @return bool
     */
    public function deleteBuyerInfo($id)
    {
        return $this->deleteById($id);
    }

    /**
     *
     * @param Buyer $buyer
     */
    public function setStatusToAvailable(Buyer $buyer)
    {
        $buyer->is_available = true;
        $buyer->save();
    }

    /**
     *
     * @param Buyer $buyer
     */
    public function setStatusToUnavailable(Buyer $buyer)
    {
        $buyer->is_available = false;
        $buyer->save();
    }

    /**
     * 获取所有可获得买家信息
     * @param $page
     * @return mixed
     */
    public function getAvailableBuyersWithPaginate($page)
    {
        return $this->model->AvailableBuyer()->orderBy('buyer_id','desc')->paginate($page);
    }
}