<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{

    protected $model;

    function __construct(Category $model)
    {
        $this->model = $model;
    }


    /**
     * 新建一个类目
     *
     * @param array $data
     * @return \App\Models\Category|null
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * 更新指定类目
     *
     * @param \App\Models\Category $updatingCategory
     * @param array $data
     * @return \App\Models\Category
     */
    public function updateCategory(Category $updatingCategory, array $data)
    {
        return $updatingCategory->update($data);
    }


    /**
     * 对指定类目增加子类目
     *
     * @param \App\Models\Category $parentCategory
     * @param array $newSubCategoryIds
     * @return bool
     */
    public function addSubCategories(Category $parentCategory, array $newSubCategoryIds)
    {
        $parentCategory_id = $parentCategory->category_id;

        DB::beginTransaction();

        foreach($newSubCategoryIds as $newSubCategoryId) {
            $subCategory = $this->getById($newSubCategoryId);
            $subCategory->setParentCategory($parentCategory_id);
        }

        DB::commit();
        return true;


    }

    /**
     * 获取指定类目的父类目
     *
     * @param \App\Models\Category $category
     * @return \App\Models\Category
     */
    public function getParentCategory(Category $category)
    {
        return $category->parentCategory;
    }


    /**
     * 删除指定类目的子类目
     *
     * @param Category $category
     * @param array $subCategoryIds
     * @return bool
     */
    public function deleteSubCategories(Category $category, array $subCategoryIds)
    {
        DB::beginTransaction();

        foreach($subCategoryIds as $subCategoryId) {
            $subCategory = $this->getById($subCategoryId);
            $subCategory->deleteParentCategory();
        }

        DB::commit();
        return true;
    }

    /**
     * 设置指定类目的父类目
     *
     * @param \App\Models\Category $category
     * @param $parentCategoryId
     * @return bool
     */
    public function setParentCategory(Category $category, $parentCategoryId)
    {
        return $category->setParentCategory($parentCategoryId);
    }


    /**
     * 删除指定类目的父类目
     *
     * @param \App\Models\Category $category
     * @return bool
     */
    public function deleteParentCategory(Category $category)
    {
        return $category->deleteParentCategory();
    }

    /**
     * 删除指定类目
     *
     * @param $categoryId
     * @return bool
     */
    public function deleteById($categoryId)
    {
        $category = $this->getById($categoryId);
        return $category->deleteCategory();
    }

    /**
     * 判断指定类目是否是叶子类目
     *
     * @param \App\Models\Category $category
     * @return bool
     */
    public function isLeafCategory(Category $category)
    {
        return (!$category->subCategories()->get()->count());
    }

    /**
     * 获得所有的类目
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    public function deleteAllSubCategories(Category $category)
    {
        return $category->deleteAllSubCategories();
    }
}