<?php namespace DreamFactory\Enterprise\Dashboard\Http;

use DreamFactory\Enterprise\Common\Traits\CustomLogPath;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
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
}
