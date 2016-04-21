<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supporter extends Model
{
    //
    protected $primaryKey = "supporter_id";

    protected $fillable = ['master_id','support_id'];

    public function forwardUser()
    {
        return $this->belongsTo('App\Models\User','hlj_id');
    }

    public function support_user()
    {
        return $this->hasOne('App\Models\User','hlj_id','support_id');
    }

}
