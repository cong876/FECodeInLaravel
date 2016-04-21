<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualCategory extends Model
{
    protected $primaryKey = "virtual_category_id";
    protected $fillable = ['virtual_category_name'];

    /**
     * 返回虚拟类目下对应的类目
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    /**
     * 在虚拟类目中增加一个类目，去重由调用的Repository保障
     *
     * @param int $categoryId
     * @return bool
     */
    public function addCategory($categoryId)
    {
        $this->categories()->attach($categoryId);
        return true;
    }

    /**
     * 在虚拟类目中删除一个类目
     *
     * @param int $categoryId
     * @return int
     */
    public function deleteCategory($categoryId)
    {
        return $this->categories()->detach($categoryId);
    }

    /**
     * 获取虚拟类目的名称
     *
     * @return string
     */
    public function getVirtualCategoryName() {
        return $this->virtual_category_name;
    }

    /**
     * 设置虚拟类目的名称
     *
     * @param $name
     * @return bool
     */
    public function setVirtualCategoryName($name) {
        $this->virtual_category_name = $name;
        return $this->save();
    }


}
