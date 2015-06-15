<?php
//******************************************************************************
//* Application Autoloader
//******************************************************************************

define('LARAVEL_START', microtime(true));

if (!function_exists('__dfe_autoload')) {
    /**
     * Bootstrap DFE
     *
     * @return bool
     */
    function __dfe_autoload()
    {
        //  Register The Composer Auto Loader
        $_basePath = dirname(__DIR__);
        require $_basePath . '/vendor/autoload.php';

        //  Laravel 5.1
        if (file_exists(__DIR__ . '/cache/compiled.php')) {
            /** @noinspection PhpIncludeInspection */
            require __DIR__ . '/cache/compiled.php';
        }

        return true;
    }
}

return __dfe_autoload();
