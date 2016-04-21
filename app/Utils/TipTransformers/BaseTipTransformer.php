<?php

namespace App\Utils\TipTransformers;

use App\Models\Tip;
use DB;

abstract class BaseTipTransformer
{

    public static function store(array $data, $from)
    {
        $instance = new static();
        $transFormedTips = $instance->transform($data);
        foreach ($transFormedTips as $tip) {
            $tip['abstracted_from'] = $from;
            Tip::create($tip);
        }

        return $transFormedTips;

    }

    abstract public function transform(array $data);

    public function userTextEncode($str)
    {
        if (!is_string($str)) return $str;
        if (!$str || $str == 'undefined') return '';

        $text = json_encode($str);
        $text = preg_replace(<<<EMOJI
/(\\\u[ed][0-9a-f]{3})/i
EMOJI
            , "", $text);
        return json_decode($text);
    }

    public function notExist($uid)
    {
        return DB::table('tips')->where('uid', $uid)->count() == 0;
    }
}