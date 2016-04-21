<?php

namespace App\Utils\Curl;


class CurlRequester
{
    private $ch;

    function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    public function setUrl($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }

    public function setHeader(array $header) {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
    }

    public function setMethod($method)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    public function setData(array $data)
    {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    public function executeAndReturnPhpObject()
    {
        $ret = curl_exec($this->ch);
        return json_decode($ret);
    }

    public function executeAndReturnPhpArray()
    {
        $ret = curl_exec($this->ch);
        return json_decode($ret, true);
    }
}