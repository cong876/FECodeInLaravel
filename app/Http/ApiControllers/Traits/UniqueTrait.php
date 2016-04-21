<?php

namespace App\Http\ApiControllers\Traits;

use Illuminate\Support\Facades\DB;

trait UniqueTrait
{
    private function exist($table, $column, $value)
    {
        if ( DB::table($table)->where($column, $value)->first() ) {
            return true;
        } else {
            return false;
        }

    }

}