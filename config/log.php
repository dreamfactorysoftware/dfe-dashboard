<?php
//******************************************************************************
//* Logging configuration
//******************************************************************************
return [
    'base-path'        => env('DFE_BASE_LOG_PATH', storage_path('logs')),
    'log-app-name'     => env('DFE_LOG_APP_NAME', 'dashboard'),
    'default-log-name' => env('DFE_DEFAULT_LOG_NAME', 'default.log'),
    'log-name-pattern' => env('DFE_LOG_NAME_PATTERN', '{stream}.log'),
    'streams'          => [
        ['driver' => 'single', 'name' => null],
    ],
];
