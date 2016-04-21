<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'App\Http\Middleware\VerifyCsrfToken',
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => 'App\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
        'operator' => 'App\Http\Middleware\OperatorMiddleWare',
        'wechatauth' => 'App\Http\Middleware\WechatAuth',
        'seller' => 'App\Http\Middleware\SellerMiddleWare',
        'super' => 'App\Http\Middleware\SuperMiddleWare',
        'group' => 'App\Http\Middleware\GroupMiddleWare',
        'candy' => 'App\Http\Middleware\CandyPromotionMiddleWare',
        'forward' => 'App\Http\Middleware\ForwardMiddleware',
        'subscribe' => 'App\Http\Middleware\SubscribeMiddleWare',
        'createGold' => 'App\Http\Middleware\CreateGoldAccountMiddleWare',
        'goldCache' => 'App\Http\Middleware\ForwardGoldCacheMiddleWare',
        'api_access' => 'App\Http\Middleware\ApiAccessMiddleware',
        'jwt.auth' => 'Tymon\JWTAuth\Middleware\GetUserFromToken',
        'jwt.refresh' => 'Tymon\JWTAuth\Middleware\RefreshToken',
    ];
}
