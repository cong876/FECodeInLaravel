<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository
{
    /**
     * 获得指定Id的模型对象
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }

    }
    
    /**
     * 删除指定Id的模型对象
     *
     * @param $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->getById($id)->delete();

    }

    /**
     * 获得模型对象的数量
     *
     * @return Number
     */
    public function getCount()
    {
        return $this->model->count();
    }


}