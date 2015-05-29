<?php namespace DreamFactory\Enterprise\Dashboard\Http;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;

class Kernel extends HttpKernel
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'DreamFactory\Enterprise\Dashboard\Http\Middleware\VerifyCsrfToken',
    ];
    /** @inheritdoc */
    protected $routeMiddleware = [
        'auth'       => 'DreamFactory\Enterprise\Dashboard\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest'      => 'DreamFactory\Enterprise\Dashboard\Http\Middleware\RedirectIfAuthenticated',
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct( Application $app, Router $router )
    {
        parent::__construct( $app, $router );

        foreach ( $this->bootstrappers as &$_strapper )
        {
            if ( $_strapper === 'Illuminate\Foundation\Bootstrap\ConfigureLogging' )
            {
                $_strapper = 'DreamFactory\Enterprise\Common\Bootstrap\CommonLoggingConfiguration';
                break;
            }
        }
    }
}
