<?php

namespace App\Repositories\Seller;

use App\Models\Seller;
use App\Repositories\BaseRepository;

class SellerRepository extends BaseRepository implements SellerRepositoryInterface
{
    protected $model;

    public function __construct(Seller $model)
    {
        $this->model = $model;
    }

    /**
     * 生成买手信息
     * @param array $data
     * @param $hlj_id
     * @return static
     */
    public function createSellerInfo(array $data, $hlj_id)
    {
        $data['hlj_id'] = $hlj_id;
        return $this->model->create($data);
    }

    /**
     * 展示买手信息
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function showAllSellerInfo()
    {
        return $this->model->all();
    }

    /**
     * 依据国家获取买手信息
     * @param $country_id
     * @return mixed
     */
    public function getSellerByCountry($country_id)
    {
        return $this->model->SearchSeller($country_id)->get();
    }

    /**
     *
     * @param Seller $seller
     * @return mixed
     */
    public function getUserInfoBySellerInfo(Seller $seller)
    {
        return $seller->user()->first();
    }

    /**
     * 展示买手信息
     * @param $id
     * @return array
     */
    public function showSellerInfo($id)
    {
        $str = [];
        $str['SellerInfo'] = $this->getById($id);
        $str['email'] = $this->getById($id)->user->email;
        return $str;
    }

    /**
     * 更新买手信息
     * @param Seller $seller
     * @param array $data
     * @return bool|int
     */
    public function updateSellerInfo(Seller $seller, array $data)
    {
        return $seller->update($data);
    }

    /**
     * 删除买手信息
     * @param $id
     * @return bool
     */
    public function deleteSellerInfo($id)
    {
        return $this->deleteById($id);
    }

    /**
     *
     * @param Seller $seller
     */
    public function setStatusToAvailable(Seller $seller)
    {
        $seller->is_available = true;
        $seller->save();
    }

    /**
     *
     * @param Seller $seller
     */
    public function setStatusToUnavailable(Seller $seller)
    {
        $seller->is_available = false;
        $seller->save();
    }

    /**
     * 展示所有非小黑屋状态买手
     * @param $page
     * @return mixed
     */
    public function getAllAvailableSellersWithPaginate($page)
    {
        return $this->model->AvailableSeller()->orderBy('seller_success_orders_num','desc')->paginate($page);
    }
}




