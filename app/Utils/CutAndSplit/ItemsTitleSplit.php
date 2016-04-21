<?php

namespace App\Utils\CutAndSplit;


class ItemsTitleSplit
{
    /**
     * 标题截取至8位
     * @param $items
     * @param int $length
     * @return string
     */
    public static function splitTitle($items, $length=8)
    {
        $title = collect($items).implode('title', ';');
        return mb_strlen($title) > $length ? mb_substr($title, 0, $length) . '...' : $title . '。';
    }

}