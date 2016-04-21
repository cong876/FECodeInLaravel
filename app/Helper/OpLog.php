<?php
/**
 * Created by PhpStorm.
 * User: caolixiang
 * Date: 15/7/31
 * Time: 上午10:28
 */

namespace App\Helper;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class OpLog
{



    public static function writeOpLog($content,  array $params) {
        $dateFormat = "Y-m-d G:i:s";
        $output = "[%datetime%] %channel%.%level_name%  %message% %context%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream= new StreamHandler(storage_path("/logs/OpLog-". date('Y-m-d') .".log"), Logger::INFO);
        $stream->setFormatter($formatter);
        $op_log = new Logger('o');
        $op_log->pushHandler($stream);
        $op_log->addInfo($content, $params);
    }




}