<?php
//******************************************************************************
//* Application Bootstrap
//******************************************************************************

if (!function_exists('__dfe_bootstrap')) {
    /**
     * @return \Illuminate\Foundation\Application
     */
    function __dfe_bootstrap()
    {
        //  Create the app
        $_app = new Illuminate\Foundation\Application(realpath(dirname(__DIR__)));

        //  Bind our default services
        $_app->singleton('Illuminate\Contracts\Http\Kernel', 'DreamFactory\Enterprise\Dashboard\Http\Kernel');
        $_app->singleton('Illuminate\Contracts\Console\Kernel', 'DreamFactory\Enterprise\Dashboard\Console\Kernel');
        $_app->singleton('Illuminate\Contracts\Debug\ExceptionHandler',
            'DreamFactory\Enterprise\Dashboard\Exceptions\Handler');

        //  Return the app
        return $_app;
    }
}

return __dfe_bootstrap();