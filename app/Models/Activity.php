<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    //
    use softDeletes;

    protected $primaryKey = 'activity_id';

    protected $fillable = ['activity_id', 'coupon', 'activity_type', 'publisher_id', 'activity_title', 'activity_info',
        'pic_urls', 'activity_start_time', 'activity_due_time', 'forward_info', 'item_order', 'is_available', 'created_at',
        'updated_at', 'deleted_at'];

    protected $casts = ['is_available' => 'boolean'];

    protected $hidden = ['activity_id', 'updated_at', 'deleted_at'];

    public function requirements()
    {
        return $this->belongsToMany('App\Models\Requirement');
    }

    public function subOrders()
    {
        return $this->belongsToMany('App\Models\SubOrder');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Item');
    }

    public function secKills()
    {
        return $this->hasMany('App\Models\Seckill');
    }

    public function scopeAllSubjectActivities($query)
    {
        return $query->where('activity_type', 2);
    }

    public function scopeAllPeriodActivities($query)
    {
        return $query->where('activity_type', 1);
    }

    public function scopeCurrentPeriod($query)
    {
        $now = Carbon::now();
        return $query->where('activity_type', 1)->whereBetween('activity_due_time', [$now->toDateTimeString(), $now->addHours(24)->toDateTimeString()]);
    }

    public function scopeTomorrowPeriod($query)
    {
        $now = Carbon::now();
        return $query->where('activity_type', 1)->whereBetween('activity_due_time', [$now->addHours(24)->toDateTimeString(), $now->addHours(48)->toDateTimeString()]);
    }

}
