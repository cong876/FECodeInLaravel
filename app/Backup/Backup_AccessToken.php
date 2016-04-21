<?php

namespace Overtrue\Wechat;

use App\Helper\WXAccessToken;

class AccessToken
{

    protected $appId;


    protected $appSecret;


    protected $token;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }
    /**
     * 获取Token
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        if($forceRefresh) {
            WXAccessToken::forgetToken();
            WXAccessToken::getToken();
        }

        // 直接通过wxToken获取AccessToken
        return WXAccessToken::getToken();
    }
}
