<?php
/**
 * Created by PhpStorm.
 * User: caolixiang
 * Date: 15/6/17
 * Time: 下午7:48
 */
namespace App\Repositories\Category;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function create(array $data);

    public function updateCategory(Category $updatingCategory, array $data);

    public function addSubCategories(Category $parentCategory, array $newSubCategoryIds);

    public function deleteSubCategories(Category $Category, array $subCategoryIds);

    public function getParentCategory(Category $Category);

    public function setParentCategory(Category $category, $parentCategoryId);

    public function deleteParentCategory(Category $category);

    public function isLeafCategory(Category $category);

    public function deleteAllSubCategories(Category $category);
}