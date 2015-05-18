<?php
/**
 * Bootstrap application
 */
if ( !function_exists( '__bootstrap' ) )
{
    /**
     * @return \Illuminate\Foundation\Application
     */
    function __bootstrap()
    {
        //  Create the application container
        $_app = new Illuminate\Foundation\Application( realpath( __DIR__ . '/../' ) );

        //  Bind bootstrap interfaces and return
        $_app->singleton( 'Illuminate\Contracts\Http\Kernel', 'DreamFactory\Enterprise\Dashboard\Http\Kernel' );
        $_app->singleton( 'Illuminate\Contracts\Console\Kernel', 'DreamFactory\Enterprise\Dashboard\Console\Kernel' );
        $_app->singleton( 'Illuminate\Contracts\Debug\ExceptionHandler', 'DreamFactory\Enterprise\Dashboard\Exceptions\Handler' );

        return $_app;
    }
}

return __bootstrap();
