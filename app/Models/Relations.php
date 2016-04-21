<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relations extends Model
{
    //
    protected $primaryKey = 'relation_id';

    protected $fillable = ['user1_id','user2_id'];

}
