<?php

namespace App\Helper;


class SingleBase
{
    private static $instance = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}