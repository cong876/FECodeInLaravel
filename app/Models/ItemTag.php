<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTag extends Model
{
    protected $primaryKey = 'item_tag_id';

    protected $fillable = ['item_tag_id', 'tag_name', 'tag_description',
        'tag_attributes', 'operator_id', 'priority',
        'exclusive', 'is_available', 'hide', 'avail_time',
        'due_time'];

    protected $hidden = ['exclusive', 'hide', 'avail_time', 'due_time'];

    public function operator()
    {
        return $this->belongsTo('App\Models\Employee', 'operator_id');
    }

    public function scopeAvailableTags($query)
    {
        return $query->where('is_available', 1)->where('hide', 0);
    }

}
