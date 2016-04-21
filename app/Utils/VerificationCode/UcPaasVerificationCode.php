<?php

namespace App\Utils\VerificationCode;

use App\Utils\ThirdParty\Ucpaas;
use Illuminate\Support\Facades\Cache;
use App\Utils\Json\ResponseTrait;
use Carbon\Carbon;

class UcpaasVerificationCode implements VerificationCodeInterface
{

    use ResponseTrait;

    private $errCode = 499;

    public function getVerificationCode($userMobile)
    {

        // 生成10分钟有效的验证码
        $verifyCode = rand(pow(10, (6 - 1)), pow(10, 6) - 1);
        $expiresAt = Carbon::now()->addMinutes(10);
        Cache::put($userMobile . '_verify', $verifyCode, $expiresAt);

        $options["accountsid"] = config('ucpass.accountId');
        $options["token"] = config('ucpass.token');
        $ucpass = new Ucpaas($options);

        //数据区域
        $appId = config('ucpass.appId');
        $templateId = "12844";
        $param = $verifyCode.",10";
        $ret = $ucpass->templateSMS($appId, $userMobile, $templateId, $param, 'json');
        $phpObject = json_decode($ret);
        if ($phpObject->resp->respCode === '000000') {
            return $this->requestSucceed();
        } else {
            return $this->requestFailed($this->errCode, "请求失败请重试");
        }
    }

    public function verifyCode($userMobile, $verificationCode)
    {
        $serverCode = Cache::get($userMobile . '_verify');
        if (!empty($serverCode) && ($serverCode == $verificationCode)) {
            return $this->requestSucceed();
        } else {
            return $this->requestFailed($this->errCode, "验证码输入有误请重试");
        }
    }
}