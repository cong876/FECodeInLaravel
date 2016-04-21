<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Category extends Model
{
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name', 'parent_category_id'];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 返回类目下所有商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Models\Item');

    }

    /**
     * 返回类目的父类目
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentCategory()
    {
        return $this->belongsTo('App\Models\Category', 'parent_category_id');
    }

    /**
     * 返回类目的直接子类目
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories()
    {
        return $this->hasMany('App\Models\Category', 'parent_category_id');
    }

    /**
     * 返回类目对应的虚拟类目
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function virtualCategories()
    {
        return $this->belongsToMany('App\Models\VirtualCategory');
    }

    /**
     * 设置类目父节点
     *
     * @param $parentCategoryId
     * @return bool|null
     */
    public function setParentCategory($parentCategoryId)
    {
        $this->parent_category_id = $parentCategoryId;
        return $this->save();
    }

    /**
     * 删除一个类目的父类目，置空
     *
     * @return bool
     */
    public function deleteParentCategory()
    {
        $this->parent_category_id = null;
        return $this->save();
    }

    /**
     * 删除一个类目的所有子类目，之前子类目的父类目置空
     *
     */
    public function deleteAllSubCategories()
    {
        DB::beginTransaction();

        $subCategories = $this->subCategories()->get();
        foreach($subCategories as $subCategory) {
            $subCategory->deleteParentCategory();
        }


        DB::commit();
        return true;
    }

    /**
     * 删除类目，之前子类目的父类目置空
     *
     * @throws \Exception
     * @return bool
     */
    public function deleteCategory() {
        DB::beginTransaction();
        $this->delete();

        DB::commit();
        return true;
    }

    /**
     * 返回类目名字
     *
     * @return string
     */
    public function getCategoryName() {
        return $this->category_name;
    }

    /**
     * 设置类目的名字
     *
     * @param $name
     * @return bool
     */
    public function setCategoryName($name) {
        $this->category_name = $name;
        return $this->save();
    }

}
