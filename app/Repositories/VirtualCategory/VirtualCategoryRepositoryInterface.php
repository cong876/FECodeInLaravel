<?php
/**
 * Created by PhpStorm.
 * User: caolixiang
 * Date: 15/6/18
 * Time: 下午2:23
 */

namespace App\Repositories\VirtualCategory;

use App\Models\VirtualCategory;

interface VirtualCategoryRepositoryInterface
{

    public function create(array $data);

    public function updateCategory(VirtualCategory $virtualCategory, array $data);

    public function getCategories(VirtualCategory $virtualCategory);

    public function addCategories(VirtualCategory $virtualCategory, array $newCategoryIds);

    public function deleteCategories(VirtualCategory $virtualCategory, array $categoryIds);

    public function getAll();

}