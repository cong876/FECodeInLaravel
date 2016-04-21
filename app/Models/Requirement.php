<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Requirement extends Model
{
    protected $primaryKey = 'requirement_id';

    protected $fillable = ['hlj_id', 'is_available', 'detail', 'main_order_id', 'country_id', 'is_activity','state'
        ,'requirement_number', 'operator_id'];

    protected $table = 'requirements';

    protected $casts = [
        'detail' => 'array',
        'is_available' => 'boolean',
        'is_activity' => 'boolean'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User','hlj_id');
    }

    public function main_order() {
        return $this->belongsTo('App\Models\MainOrder','main_order_id');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country');
    }

    public function items() {
        return $this->belongsToMany('App\Models\Item');
    }

    public function activities()
    {
        return $this->belongsToMany('App\Models\Activity');
    }

    public function requirementMemos()
    {
        return $this->hasMany('App\Models\RequirementMemo');
    }

    public function requirementDetails()
    {
        return $this->hasMany('App\Models\RequirementDetail');
    }

    public function ScopeWaitDispatch($query)
    {
        return $query->where('state', 101)->where('operator_id',0);
    }

    public function ScopeWaitResponse($query)
    {
        return $query->where('state', 101);
    }

    public function  ScopeAllWaitResponse($query)
    {
        return $query->where('state',101)->where('operator_id','>',0);
    }

    public function ScopeWaitSplit($query)
    {
        return $query->where('state', 201);
    }

    public function ScopeFinished($query)
    {
        return $query->where('state',301);
    }

    public function ScopeClosed($query)
    {
        return $query->where('state',411)->orWhere('state',431);
    }

    public function scopeWaitOffer($query)
    {
        return $query->whereIn('state', [101, 201]);
    }

    public function scopeClosedByUser($query)
    {
        return $query->where('state', 411);
    }

    public function scopeClosedByOperator($query)
    {
        return $query->where('state', 431);
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\Employee','operator_id');
    }


}
