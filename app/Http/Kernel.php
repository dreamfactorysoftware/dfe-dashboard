<?php namespace DreamFactory\Enterprise\Dashboard\Http;

use DreamFactory\Enterprise\Common\Http\Middleware\Authenticate;
use DreamFactory\Enterprise\Common\Http\Middleware\RedirectIfAuthenticated;
use DreamFactory\Enterprise\Common\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    //use CommonLogging;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ];
    /** @inheritdoc */
    protected $routeMiddleware = [
        'auth'       => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'guest'      => RedirectIfAuthenticated::class,
    ];
}
