<?php
//******************************************************************************
//* Application Cache Settings
//******************************************************************************
return [
    'default' => env( 'CACHE_DRIVER', 'file' ),
    'prefix'  => 'dfe',
    'stores'  => [
        'database'  => [
            'driver'     => 'database',
            'table'      => 'cache_t',
            'connection' => 'dfe-local',
        ],
        'file'      => [
            'driver' => 'file',
            'path'   => storage_path() . '/framework/cache',
        ],
        'memcached' => [
            'driver'  => 'memcached',
            'servers' => [
                [
                    'host'   => '127.0.0.1',
                    'port'   => 11211,
                    'weight' => 100
                ],
            ],
        ],
        'redis'     => [
            'driver'     => 'redis',
            'connection' => 'default',
        ],
    ],
];
