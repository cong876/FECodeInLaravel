<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'item_id';
    protected $dates = ['deleted_at', 'auto_on_shelf_at', 'last_toggle_shelf_at'];
    protected $fillable = ['title', 'category_id', 'country_id', 'price',
        'pic_urls', 'sku_id', 'is_on_shelf',
        'is_positive', 'is_virtual', 'is_available', 'buy_per_user',
        'hlj_id', 'attributes', 'publisher_id', 'item_type'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at',
        'item_id', 'auto_on_shelf_at', 'latest_toggle_shelf_at', 'is_available'];

    protected $casts = [
        'is_on_shelf' => 'boolean',
        'is_positive' => 'boolean',
        'is_virtual' => 'boolean',
        'is_available' => 'boolean',
        'pic_urls' => 'array',
        'attributes' => 'array',
    ];


    public function setItemUnavailable()
    {
        $this->is_available = false;
        return $this->save();
    }

    public function restoreItemAvailableState()
    {
        $this->is_available = true;
        return $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skus()
    {
        return $this->hasMany('App\Models\Sku');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'hlj_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function requirements()
    {
        return $this->belongsToMany('App\Models\Requirement');
    }

    public function sub_orders()
    {
        return $this->belongsToMany('App\Models\SubOrder');
    }

    public function main_orders()
    {
        return $this->belongsToMany('App\Models\MainOrder');
    }

    public function activities()
    {
        return $this->belongsToMany('App\Models\Activity');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail_passive()
    {
        return $this->hasOne('App\Models\DetailPassiveExtra');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail_positive()
    {
        return $this->hasOne('App\Models\DetailPositiveExtra');
    }

    public function goldsRequired()
    {
        return $this->hasOne('App\Models\TaskItem');
    }

    public function getFullDetail()
    {
        if ($this->is_positive) {
            return $this->with('skus', 'country','detail_positive')->get();
        } else {
            return $this->with('skus', 'country', 'detail_passive')->get();
        }

    }

    public function scopePositive($query)
    {
        return $query->where('is_positive', 1);
    }

    public function scopePassive($query)
    {
        return $query->where('is_positive', 0);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', 0);
    }


}
