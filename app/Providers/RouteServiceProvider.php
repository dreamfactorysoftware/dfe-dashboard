<?php namespace DreamFactory\Enterprise\Dashboard\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'DreamFactory\Enterprise\Dashboard\Http\Controllers';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace],
            function($router) {
                /** @noinspection PhpIncludeInspection */
                require app_path('Http/routes.php');
            });
    }

}
