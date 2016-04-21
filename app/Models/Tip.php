<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    protected $fillable = ['title', 'description',
        'img_urls', 'likes',
        'pub_time', 'user_info', 'uid', 'abstracted_from'];
}
