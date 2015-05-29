<?php namespace DreamFactory\Enterprise\Dashboard\Http;

use DreamFactory\Enterprise\Common\Traits\CustomLogPath;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;

class Kernel extends HttpKernel
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const CLASS_TO_REPLACE = 'Illuminate\Foundation\Bootstrap\ConfigureLogging';
    /**
     * @type string
     */
    const REPLACEMENT_CLASS = 'DreamFactory\Enterprise\Common\Bootstrap\CommonLoggingConfiguration';

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use CustomLogPath;

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
        $this->_replaceLoggingConfigurationClass( static::CLASS_TO_REPLACE, static::REPLACEMENT_CLASS );

        parent::__construct( $app, $router );
    }
}
