<?php namespace DreamFactory\Enterprise\Dashboard\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     *
     *
     * @var array HTTP middleware
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'DreamFactory\Enterprise\Dashboard\Http\Middleware\VerifyCsrfToken',
    ];
    /**
     * @var array Route middleware
     */
    protected $routeMiddleware = [
        'auth'       => 'DreamFactory\Enterprise\Dashboard\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest'      => 'DreamFactory\Enterprise\Dashboard\Http\Middleware\RedirectIfAuthenticated',
    ];

}
