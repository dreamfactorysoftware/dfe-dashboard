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
        $_version = \Cache::get('dfe.common.display-version', config('dfe.common.display-version') . '-' . substr(`git rev-parse --verify HEAD`, 0, 8));
        $this->bagAndTag('dfe.common.display-version', $_version, 'APP_VERSION');

        if (empty($_themes = \Cache::get('dashboard.theme-cache'))) {
            $_themes = config('dashboard.default-themes', []);

            foreach (config('dashboard.theme-locations', []) as $_path) {
                foreach (\File::allFiles(public_path($_path)) as $_theme) {
                    /** @type \SplFileInfo $_theme */
                    $_name = $_theme->getFilename();

                    if (is_file($_theme->getPathname()) && !in_array($_name = str_ireplace(['.min.css', '.css'], null, $_name), $_themes)) {
                        $_themes[] = $_name;
                    }
                }
            }

            //  Clean up
            foreach ($_themes as $_key => $_theme) {
                $_themes[$_key] = trim(studly_case($_theme));
            }

            sort($_themes);
        }

        $this->bagAndTag('dashboard.theme-cache', $_themes);
    }

    /**
     * @param string $key   The config key
     * @param string $env   The env key
     * @param null   $value The value
     * @param int    $ttl
     */
    private function bagAndTag($key, $value = null, $env = null, $ttl = 15)
    {
        \Cache::put($key, $value, $ttl);
        is_array($key) ? config($key) : config([$key => $value]);

        if (!empty($env)) {
            putenv($env . '=' . $value);
            $_ENV[$env] = $value;
        }
    }
}
