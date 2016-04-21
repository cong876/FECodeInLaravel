<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */

    protected $except = [
        'wechat/*',
        'wechat',
        'payment/requestPay',
        'webHook',
        'webHook/*',
        'getpay/*',
        'payment/refundOrder/*',
        'createRealCharge/*',
        'testPay/*',
        'user/createReceivingAddress',
        'user/updateReceivingAddress/*',
        'api/*'
    ];
}
