<?php

namespace App\Helper;

class SuborderNumberGeneratorHelper
{
    public static function generateSuborderNumber() {
        $currentTime = microtime(true);
        $split = explode('.', $currentTime);
        $suborderNumber = 'YE' . implode('', $split);
        return $suborderNumber;
    }
}