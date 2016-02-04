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
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
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

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function bootstrap()
    {
        parent::bootstrap();

        //  Make the version info extra cool
        if (null === ($_version = Cache::get('dfe.cool-app-version'))) {
            $_version = config('dfe.common.display-version') . '-' . substr(`git rev-parse --verify HEAD`, 0, 8);
            logger('app version realized ' . $_version);
            \Cache::put('dfe.cool-app-version', $_version, 15);
        }

        config(['dfe.common.display-version' => $_version, 'app.version' => $_version,]);
        putenv('APP_VERSION=' . $_version);
        $_ENV['APP_VERSION'] = $_version;
    }

}
