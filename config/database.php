<?php
return [
    'fetch'       => PDO::FETCH_CLASS,
    'default'     => 'dfe-local',
    'migrations'  => 'migration_t',
    //******************************************************************************
    //* Connections
    //******************************************************************************
    'connections' => [
        'dfe-local' => [
            'driver'    => env( 'DB_DRIVER', 'mysql' ),
            'host'      => env( 'DB_HOST', 'localhost' ),
            'port'      => env( 'DB_PORT', 3306 ),
            'database'  => env( 'DB_DATABASE', 'dfe_local' ),
            'username'  => env( 'DB_USERNAME', 'dfe_user' ),
            'password'  => env( 'DB_PASSWORD', 'dfe_user' ),
            'charset'   => env( 'DB_CHARSET', 'utf8' ),
            'collation' => env( 'DB_COLLATION', 'utf8_unicode_ci' ),
            'prefix'    => env( 'DB_PREFIX' ),
        ],
    ],
    'redis'       => [
        'cluster' => false,
        'default' => [
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 0,
        ],
    ],
];
