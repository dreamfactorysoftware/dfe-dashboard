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
        $_vendorPath = __DIR__ . '/../vendor';

        //  Register The Composer Auto Loader
        require $_vendorPath . '/autoload.php';

        //  Laravel 5.1+
        if (file_exists($_vendorPath . '/compiled.php')) {
            /** @noinspection PhpIncludeInspection */
            require $_vendorPath . '/compiled.php';
        }

        return true;
    }
}

return __dfe_autoload();
