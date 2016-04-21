<?php
/**
 * Created by PhpStorm.
 * User: caolixiang
 * Date: 15/6/18
 * Time: 下午2:26
 */

namespace App\Repositories\VirtualCategory;


use App\Models\VirtualCategory;
use App\Repositories\BaseRepository;

class VirtualCategoryRepository extends BaseRepository implements VirtualCategoryRepositoryInterface{

    protected $model;

    /**
     * @param VirtualCategory $model
     */
    function __construct(VirtualCategory $model)
    {
        $this->model = $model;
    }

    /**
     * 创建一个虚拟类目
     *
     * @param array $data
     * @return \App\Models\VirtualCategory|null
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * 获取指定虚拟类目的子类目
     *
     * @param VirtualCategory $virtualCategory
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategories(VirtualCategory $virtualCategory)
    {
        return $virtualCategory->categories()->get();
    }

    /**
     * 增加指定虚拟类目的子类目
     *
     * @param VirtualCategory $virtualCategory
     * @param array $newCategoryIds
     * @return bool
     */
    public function addCategories(VirtualCategory $virtualCategory, array $newCategoryIds)
    {
        $originCategories = $virtualCategory->categories()->get();
        foreach($newCategoryIds as $newCategoryId) {
            if(!$originCategories->contains($this->getById($newCategoryId))) {
                $virtualCategory->addCategory($newCategoryId);
            }
        }
        if($originCategories->count() < $virtualCategory->categories()->get()->count()) {
            return true;
        }
        return false;

    }

    /**
     * 删除指定虚拟类目的子类目，返回成功删除的个数
     *
     * @param VirtualCategory $virtualCategory
     * @param array $categoryIds
     * @return int
     */
    public function deleteCategories(VirtualCategory $virtualCategory, array $categoryIds)
    {
        $count = 0;
        foreach($categoryIds as $categoryId) {
            $count += $virtualCategory->deleteCategory($categoryId);
        }
        return $count;
    }

    /**
     * 更新一个虚拟类目
     *
     * @param VirtualCategory $updatingVirtualCategory
     * @param array $data
     * @return bool|int
     */
    public function updateCategory(VirtualCategory $updatingVirtualCategory, array $data)
    {
        return $updatingVirtualCategory->update($data);
    }

    /**
     * 获得所有虚拟类目
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }
}