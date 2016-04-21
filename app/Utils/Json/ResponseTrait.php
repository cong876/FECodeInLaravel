<?php

namespace App\Utils\Json;


trait ResponseTrait
{
    /**
     * 成功响应规范
     * @return array
     */
    public function requestSucceed()
    {
        return [
            "status_code" => 200,
            "message" => "OK"
        ];
    }

    /**
     * 失败响应规范
     * @param $status_code
     * @param $message
     * @return array
     */
    public function requestFailed($status_code, $message)
    {
        return [
            "status_code" => $status_code,
            "message" => $message
        ];
    }

}