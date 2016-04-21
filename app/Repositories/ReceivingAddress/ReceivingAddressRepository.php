<?php

namespace App\Repositories\ReceivingAddress;

use App\Models\ReceivingAddress;
use App\Repositories\BaseRepository;

class ReceivingAddressRepository extends BaseRepository implements ReceivingAddressRepositoryInterface
{
    protected $model;

    function __construct(ReceivingAddress $model)
    {
        $this->model = $model;
    }


    /**
     * 创建一个收货地址
     *
     * @param array $data
     * @param $hlj_id
     * @return \App\Models\ReceivingAddress
     */
    function create(array $data, $hlj_id)
    {
        $data ['hlj_id'] = $hlj_id;
        return $this->model->create($data);
    }

    /**
     * 更改收货地址
     *
     * @param ReceivingAddress $receivingAddress
     * @param array $data
     * @return bool|int
     */
    function update(ReceivingAddress $receivingAddress, array $data)
    {
        return $receivingAddress->update($data);
    }

    /**
     * 删除收货地址
     *
     * @param ReceivingAddress $receivingAddress
     * @return bool|null
     * @throws \Exception
     */
    function delete(ReceivingAddress $receivingAddress)
    {
        $receivingAddress->is_available = false;
        $receivingAddress->is_default = false;
        return $receivingAddress->save();
//        return $receivingAddress->delete();
    }

    /**
     * 获取收货地址信息
     *
     * @param $hlj_id
     * @return mixed
     */
    function getAddressDetail($hlj_id)
    {


        return $this->model->where('hlj_id', $hlj_id)->get();
    }

    /**
     * 获取默认地址信息
     *
     * @param $hlj_id
     * @return mixed
     */
    public function getDefaultAddress($hlj_id)
    {
        return $this->model->where('hlj_id', $hlj_id)->where('is_default', 1)->first();
    }

    /**
     * 将收货地址设为默认地址
     *
     * @param ReceivingAddress $receivingAddress
     */
    public function setAddressToDefault(ReceivingAddress $receivingAddress, $hlj_id)
    {
        if ($temp = $this->getDefaultAddress($hlj_id)) {
            $temp->is_default = false;
            $temp->save();
        }
        $receivingAddress->is_default = true;
        return $receivingAddress->save();
    }

    /**
     * 撤销该默认地址
     *
     * @param ReceivingAddress $receivingAddress
     */
    function setAddressToNormal(ReceivingAddress $receivingAddress)
    {
        $receivingAddress->is_default = false;
        return $receivingAddress->save();
    }

    /**
     *
     *
     * @param ReceivingAddress $receivingAddress
     */
    function setStatusToAvailable(ReceivingAddress $receivingAddress)
    {
        $receivingAddress->is_available = true;
        return $receivingAddress->save();
    }

    /**
     *
     *
     * @param ReceivingAddress $receivingAddress
     */
    function setStatusToUnavailable(ReceivingAddress $receivingAddress)
    {
        $receivingAddress->is_available = false;
        return $receivingAddress->save();
    }
}