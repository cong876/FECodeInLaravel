<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    // API控制器在该命名空间内定义
    protected $api_controller_namespace = 'App\Http\ApiControllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->configureAPIRoute();

        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/routes.php');
        });
    }

    /**
     * 配置 API 路由.
     */
    public function configureAPIRoute()
    {
        $api_router = app('Dingo\Api\Routing\Router');
        $api_router->group([
            'version'   => config('api.version'),
            'namespace' => $this->api_controller_namespace,
        ], function ($api) {
            require app_path('Http/api_routes.php');
        });
    }
}
