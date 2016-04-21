<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequirementMemo extends Model
{
    //
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'requirement_memo_id';

    protected $fillable = ['hlj_id','requirement_id','content','state','created_at'];

    protected $hidden = ['requirement_memo_id','state','updated_at'];

    public function requirement()
    {
        return $this->belongsTo('App\Models\Requirement','requirement_id');
    }

    public function scopeSearchMemo($query,$requirement_id)
    {
        return $query->where('requirement_id',$requirement_id);
    }

}
