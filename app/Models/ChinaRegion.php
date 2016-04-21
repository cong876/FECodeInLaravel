<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChinaRegion extends Model
{
    protected $primaryKey = 'china_region_id';
    public $timestamps = false;

    public function parentRegion()
    {
        return $this->belongsTo('App\Models\ChinaRegion','parent_id');
    }

    public function childRegions()
    {
        return $this->hasMany('App\Models\ChinaRegion', 'parent_id');
    }

    public function scopeProvinceLevel($query)
    {
        return $query->where('level',1);
    }

}
