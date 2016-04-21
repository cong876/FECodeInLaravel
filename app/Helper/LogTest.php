<?php
namespace App\Helper;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogTest
{
    private $model;

    protected function __construct(Logger $model)
    {
        $this->model = $model;
    }

    protected static function createFormat()
    {
        $dateFormat = 'Y-m-d H:i:s';
        $output = "[%datetime%] %channel%: %message% %context%\n";
        $formatter = new LineFormatter($output,$dateFormat);
        return $formatter;
    }

    public static function writeTestLog($contents,array $extra)
    {
        $formatter = LogTest::createFormat();
        $stream= new StreamHandler(storage_path('/logs/yeyeLog/OpLog-'. date('Y-m-d') .".log"), Logger::INFO);
        $stream->setFormatter($formatter);
        $logger = new Logger('操作');
        $logger->pushHandler($stream);
        $logger->addInfo($contents, $extra);

    }

    public static function UpdateSellerLog($contents,array $extra)
    {
        $formatter = LogTest::createFormat();
        $stream= new StreamHandler(storage_path('/logs/yeyeLog/UpdateSeller-log'),Logger::INFO);
        $stream->setFormatter($formatter);
        $logger = new Logger('更换买手');
        $logger->pushHandler($stream);
        $logger->addInfo($contents, $extra);
    }

    public static function writeSellerLog($contents, array $extra)
    {
        $formatter = LogTest::createFormat();
        $stream = new StreamHandler(storage_path('/logs/yeyeLog/Seller-'.date('Y-m-d').'.log'),Logger::INFO);
        $stream->setFormatter($formatter);
        $logger = new Logger('买手操作');
        $logger->pushHandler($stream);
        $logger->addInfo($contents,$extra);
    }

    public static function writeBuyerLog($contents,array $extra)
    {
        $formatter = LogTest::createFormat();
        $stream = new StreamHandler(storage_path('/logs/yeyeLog/Buyer-'.date('Y-m-d').'.log'),Logger::INFO);
        $stream->setFormatter($formatter);
        $logger = new Logger('买家操作');
        $logger->pushHandler($stream);
        $logger->addInfo($contents,$extra);
    }


}