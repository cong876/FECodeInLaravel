<?php

namespace App\Utils\VerificationCode;

interface VerificationCodeInterface
{
    public function getVerificationCode($userMobile);
    public function verifyCode($userMobile, $verificationCode);

}