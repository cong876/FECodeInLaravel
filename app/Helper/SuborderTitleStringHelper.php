<?php

namespace App\Helper;

use App\Models\SubOrder;

class SuborderTitleStringHelper
{
    public static function getTitleStringAtLength(SubOrder $sub, $length = 12) {
        $items = $sub->items;
        $title = '';
        foreach($items as $item)
        {
            $title .= $item->title . ';';
        }
        $title = rtrim($title,';');
        if (mb_strlen($title) > 8) {
            return mb_substr($title, 0, $length) . '...';
        } else {
            return $title . '。';
        }
    }
}