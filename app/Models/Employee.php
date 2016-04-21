<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'employee_id';
    protected $fillable = ['title', 'hlj_id', 'ye_code', 'real_name', 'name_pinyin', 'identity_card_no',
                            'name_abbreviation','type', 'op_level', 'entry_date',
                            'is_available', 'entry_date', 'birthday'];
    protected $dates = ['entry_date', 'birthday'];


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'hlj_id');
    }

}
