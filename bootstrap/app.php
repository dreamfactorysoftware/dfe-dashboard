<?php
//  Create the application container
$app = new Illuminate\Foundation\Application( realpath( __DIR__ . '/../' ) );

//  Bind bootstrap interfaces and return
$app->singleton( 'Illuminate\Contracts\Http\Kernel', 'DreamFactory\Enterprise\Dashboard\Http\Kernel' );
$app->singleton( 'Illuminate\Contracts\Console\Kernel', 'DreamFactory\Enterprise\Dashboard\Console\Kernel' );
$app->singleton( 'Illuminate\Contracts\Debug\ExceptionHandler', 'DreamFactory\Enterprise\Dashboard\Exceptions\Handler' );

return $app;
