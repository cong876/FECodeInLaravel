<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementDetail extends Model
{
    protected $primaryKey = 'requirement_detail_id';

    protected $casts = ['pic_urls' => 'array'];

    protected $fillable = ['requirement_id', 'number', 'title', 'description', 'pic_urls', 'state'];

    public function requirement()
    {
        return $this->belongsTo('App\Models\Requirement');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }

    public function setRequirementDetailUnavailable()
    {
        $this->is_available = false;
        return $this->save();
    }
}
