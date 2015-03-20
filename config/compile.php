<?php
//******************************************************************************
//* Compile/Optimize Settings
//******************************************************************************
return [
    'files'     => [
        realpath( __DIR__ . '/../app/Providers/AppServiceProvider.php' ),
        realpath( __DIR__ . '/../app/Providers/BusServiceProvider.php' ),
        realpath( __DIR__ . '/../app/Providers/ConfigServiceProvider.php' ),
        realpath( __DIR__ . '/../app/Providers/EventServiceProvider.php' ),
        realpath( __DIR__ . '/../app/Providers/RouteServiceProvider.php' ),
    ],
    'providers' => [],
];
