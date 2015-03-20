<?php
//  Create the application container
$app = new Illuminate\Foundation\Application( realpath( __DIR__ . '/../' ) );

//  Bind bootstrap interfaces and return
$app->singleton( 'Illuminate\Contracts\Http\Kernel', 'App\Http\Kernel' );
$app->singleton( 'Illuminate\Contracts\Console\Kernel', 'App\Console\Kernel' );
$app->singleton( 'Illuminate\Contracts\Debug\ExceptionHandler', 'App\Exceptions\Handler' );

return $app;
