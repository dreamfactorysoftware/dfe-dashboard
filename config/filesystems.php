<?php
return [
    //  Default Filesystem Disk
    'default' => 'local',
    //  Default Cloud Filesystem Disk
    'cloud'   => 's3',
    //  Filesystem Disks
    'disks'   => [
        'local'  => [
            'driver' => 'local',
            'root'   => storage_path() . '/app',
        ],
        //  hosted storage
        'hosted' => [
            'driver' => 'local',
            'root'   => '/data/storage',
        ]
    ],
];
