<?php
use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;

return [
    //  Default connection
    'default'     => 'local',
    //  Connections
    'connections' => [
        //  Generalized local storage
        'local'            => [
            'driver' => 'local',
            'path'   => env('DFE_HOSTED_BASE_PATH', EnterpriseDefaults::STORAGE_ROOT),
        ],
        //  cluster-east-2 hosted storage
        'cluster-east-2'   => [
            'driver' => 'local',
            'path'   => env('DFE_HOSTED_BASE_PATH', EnterpriseDefaults::STORAGE_ROOT),
        ],
        //  mount-east-1 hosted storage
        'mount-east-1'     => [
            'driver' => 'local',
            'path'   => env('DFE_HOSTED_BASE_PATH', EnterpriseDefaults::STORAGE_ROOT),
        ],
        //  dfe-mount-east-1 hosted storage
        'dfe-mount-east-1' => [
            'driver' => 'local',
            'path'   => env('DFE_HOSTED_BASE_PATH', EnterpriseDefaults::STORAGE_ROOT),
        ],
        'mount-local-1'    => [
            'driver' => 'local',
            'path'   => env('DFE_HOSTED_BASE_PATH', EnterpriseDefaults::STORAGE_ROOT),
        ],

    ],
    //  Cache
    'cache'       => [
        'foo'     => [
            'driver'    => 'illuminate',
            'connector' => null, // null means use default driver
            'key'       => 'foo',
            // 'ttl'       => 300
        ],
        'bar'     => [
            'driver'    => 'illuminate',
            'connector' => 'redis', // config/cache.php
            'key'       => 'bar',
            'ttl'       => 600,
        ],
        'adapter' => [
            'driver'  => 'adapter',
            'adapter' => 'local', // as defined in connections
            'file'    => 'flysystem.json',
            'ttl'     => 600,
        ],
    ],
];
