<?php
//******************************************************************************
//* Application Autoloader
//******************************************************************************

if (!function_exists( '__dfe_autoload' )) {
    define( 'LARAVEL_START', microtime( true ) );

    /**
     * Bootstrap DFE
     *
     * @return bool
     */
    function __dfe_autoload()
    {
        //  Register The Composer Auto Loader
        $_basePath = dirname( __DIR__ );
        require $_basePath . '/vendor/autoload.php';

        //  Laravel 5.0
        if (file_exists( __DIR__ . '/cache/compiled.php' )) {
            /** @noinspection PhpIncludeInspection */
            require __DIR__ . '/cache/compiled.php';
        } elseif (file_exists( $_basePath . '/storage/framework/compiled.php' )) {
            /** @noinspection PhpIncludeInspection */
            require $_basePath . '/storage/framework/compiled.php';
        }

        return true;
    }
}

return __dfe_autoload();
