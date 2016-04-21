<?php

namespace App\Utils\VerificationCode;

use App\Utils\Curl\CurlRequester;
use App\Utils\Json\ResponseTrait;


class LeanCloudVerificationCode implements VerificationCodeInterface
{
    use ResponseTrait;

    private $errCode = 499;

    /**
     * 向LeanCloud请求语音验证码
     * @param $userMobileL
     * @return array
     */
    public function getVerificationCode($userMobile)
    {
        $requester = new CurlRequester();
        $requester->setUrl("https://api.leancloud.cn/1.1/requestSmsCode");
        $requester->setMethod("POST");
        $requester->setData(array(
            "mobilePhoneNumber" => $userMobile,
            "smsType" => "voice"
        ));
        $requester->setHeader(array(
            "X-LC-Id: ". config('leancloud.appId'),
            "X-LC-Key: ". config('leancloud.appKey'),
            "Content-Type: application/json"
        ));
        $ret =  $requester->executeAndReturnPhpObject();
        if (empty($ret->error)) {
            return $this->requestSucceed();
        } else {
            return $this->requestFailed($this->errCode, $ret->error);
        }

    }

    /**
     * 判断用户输入的验证码是否正确
     * @param $userMobile
     * @param $verificationCode
     * @return array
     */
    public function verifyCode($userMobile, $verificationCode)
    {

        $requester = new CurlRequester();
        $requester->setUrl("https://api.leancloud.cn/1.1/verifySmsCode/" . $verificationCode .
            "?mobilePhoneNumber=" . $userMobile);
        $requester->setMethod("POST");
        $requester->setHeader(array(
            "X-LC-Id: ". config('leancloud.appId'),
            "X-LC-Key: ". config('leancloud.appKey'),
            "Content-Type: application/json"
        ));
        $ret = $requester->executeAndReturnPhpObject();
        if (empty($ret->error)) {
            return $this->requestSucceed();
        } else {
            return $this->requestFailed($this->errCode, $ret->error);
        }
    }

}