<?php

return [
    //  Default Queue Driver
    'default'     => env( 'QUEUE_DRIVER', 'sync' ),
    //  Queue Connections
    'connections' => [
        'sync'     => [
            'driver' => 'sync',
        ],
        'database' => [
            'driver'   => 'database',
            'table'    => 'job_t',
            'queue'    => 'default',
            'expire'   => 60,
            'database' => 'dfe-local',
        ],
    ],
    //  Failed Queue Jobs
    'failed'      => [
        'database' => 'dfe-local',
        'table'    => 'job_fail_t',
    ],
];
