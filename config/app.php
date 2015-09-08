<?php
return [
    //******************************************************************************
    //* Application Settings
    //******************************************************************************
    'debug'           => env('APP_DEBUG'),
    'url'             => env('APP_URL'),
    'timezone'        => 'America/New_York',
    'locale'          => 'en',
    'fallback_locale' => 'en',
    'key'             => env('APP_KEY'),
    'cipher'          => MCRYPT_RIJNDAEL_128,
    'log'             => 'single',
    //******************************************************************************
    //* Autoloaded Providers
    //******************************************************************************
    'providers'       => [
        /** Laravel Framework Service Providers... */
        Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Routing\ControllerServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        /** Application Service Providers... */
        DreamFactory\Enterprise\Dashboard\Providers\AppServiceProvider::class,
        DreamFactory\Enterprise\Dashboard\Providers\BusServiceProvider::class,
        DreamFactory\Enterprise\Dashboard\Providers\ConfigServiceProvider::class,
        DreamFactory\Enterprise\Dashboard\Providers\EventServiceProvider::class,
        DreamFactory\Enterprise\Dashboard\Providers\RouteServiceProvider::class,
        DreamFactory\Enterprise\Dashboard\Providers\DashboardServiceProvider::class,
        /** DreamFactory Common Providers */
        DreamFactory\Enterprise\Common\Providers\LibraryAssetsProvider::class,
        DreamFactory\Enterprise\Common\Providers\InstanceStorageServiceProvider::class,
        DreamFactory\Enterprise\Common\Providers\Auth\DashboardAuthProvider::class,
        /** DreamFactory Storage Provider */
        DreamFactory\Enterprise\Storage\Providers\MountServiceProvider::class,
        /** DreamFactory Ops Client Provider */
        DreamFactory\Enterprise\Console\Ops\Providers\OpsClientServiceProvider::class,
        /** DreamFactory Partner Services Provider */
        DreamFactory\Enterprise\Partner\Providers\PartnerServiceProvider::class,
        /** 3rd-party Service Providers */
        Marwelln\Recaptcha\RecaptchaServiceProvider::class,
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        GrahamCampbell\Flysystem\FlysystemServiceProvider::class,
    ],
    //******************************************************************************
    //* Aliases
    //******************************************************************************
    'aliases'         => [
        'App'             => Illuminate\Support\Facades\App::class,
        'Artisan'         => Illuminate\Support\Facades\Artisan::class,
        'Auth'            => Illuminate\Support\Facades\Auth::class,
        'Blade'           => Illuminate\Support\Facades\Blade::class,
        'Bus'             => Illuminate\Support\Facades\Bus::class,
        'Cache'           => Illuminate\Support\Facades\Cache::class,
        'Config'          => Illuminate\Support\Facades\Config::class,
        'Cookie'          => Illuminate\Support\Facades\Cookie::class,
        'Crypt'           => Illuminate\Support\Facades\Crypt::class,
        'DB'              => Illuminate\Support\Facades\DB::class,
        'Eloquent'        => Illuminate\Database\Eloquent\Model::class,
        'Event'           => Illuminate\Support\Facades\Event::class,
        'File'            => Illuminate\Support\Facades\File::class,
        'Hash'            => Illuminate\Support\Facades\Hash::class,
        'Input'           => Illuminate\Support\Facades\Input::class,
        'Inspiring'       => Illuminate\Foundation\Inspiring::class,
        'Lang'            => Illuminate\Support\Facades\Lang::class,
        'Log'             => Illuminate\Support\Facades\Log::class,
        'Mail'            => Illuminate\Support\Facades\Mail::class,
        'Password'        => Illuminate\Support\Facades\Password::class,
        'Queue'           => Illuminate\Support\Facades\Queue::class,
        'Redirect'        => Illuminate\Support\Facades\Redirect::class,
        'Redis'           => Illuminate\Support\Facades\Redis::class,
        'Request'         => Illuminate\Support\Facades\Request::class,
        'Response'        => Illuminate\Support\Facades\Response::class,
        'Route'           => Illuminate\Support\Facades\Route::class,
        'Schema'          => Illuminate\Support\Facades\Schema::class,
        'Session'         => Illuminate\Support\Facades\Session::class,
        'Storage'         => Illuminate\Support\Facades\Storage::class,
        'URL'             => Illuminate\Support\Facades\URL::class,
        'Validator'       => Illuminate\Support\Facades\Validator::class,
        'View'            => Illuminate\Support\Facades\View::class,
        /** DreamFactory aliases */
        'Dashboard'       => DreamFactory\Enterprise\Dashboard\Facades\Dashboard::class,
        'InstanceStorage' => DreamFactory\Enterprise\Common\Facades\InstanceStorage::class,
        'Mounter'         => DreamFactory\Enterprise\Storage\Facades\Mounter::class,
        'Partner'         => DreamFactory\Enterprise\Partner\Facades\Partner::class,
        /** Third-party aliases */
        'Flysystem'       => GrahamCampbell\Flysystem\Facades\Flysystem::class,
    ],

];
