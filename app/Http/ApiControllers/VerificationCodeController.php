<?php

namespace App\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Utils\VerificationCode\UcpaasVerificationCode;
use App\Utils\VerificationCode\LeanCloudVerificationCode;
use App\Utils\Json\ResponseTrait;

class VerificationCodeController extends Controller
{

    use ResponseTrait;

    private $smsCenter;
    private $userMobile;
    private $errCode = 451;

    /**
     * 通过手机号码换取验证码
     * @param Request $request
     * @return mixed
     */
    public function getSMSVerifyCode(Request $request)
    {
        $userMobile = $request->input('mobile');
        $zone = $request->input('zone');
        if($zone && $userMobile) {
            $this->setSMSCenterAndMobileNumberFormat($zone, $userMobile);
            $ret = $this->smsCenter->getVerificationCode($this->userMobile);
        }else {
            $ret = $this->requestFailed($this->errCode, "请求参数不完整");
        }
        return $this->response->array($ret);
    }

    /**
     * 验证注册码是否正确
     * @param Request $request
     * @return mixed
     */
    public function verifySMSCode(Request $request)
    {
        $userMobile = $request->input('mobile');
        $verifyCode = $request->input('verifyCode');
        $zone = $request->input('zone');

        if($userMobile && $verifyCode && $zone) {
            $this->setSMSCenterAndMobileNumberFormat($zone, $userMobile);
            $ret = $this->smsCenter->verifyCode($this->userMobile, $verifyCode);
        }else {
            $ret = $this->requestFailed($this->errCode, "请求参数不完整");
        }
        return $this->response->array($ret);
    }

    /**
     * 判断调用接口为国内(86 ---leancloud)还是国外(云之讯)
     * @param $zone
     * @param $userMobile
     */
    private function setSMSCenterAndMobileNumberFormat($zone, $userMobile)
    {
        if( $zone + 0 === 86) {
            $this->smsCenter = new LeanCloudVerificationCode();
            $this->userMobile = $userMobile;
        } else {
            $this->smsCenter = new UcpaasVerificationCode();
            $this->userMobile = '00' . $zone . $userMobile;
        }
    }
}