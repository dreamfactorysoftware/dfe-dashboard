<?php
return [
    //  Default Filesystem Disk
    'default' => 'local',
    //  Default Cloud Filesystem Disk
    'cloud'   => 's3',
    //  Filesystem Disks
    'disks'   => [
        'local'   => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],
        'uploads' => [
            'driver' => 'local',
            'root'   => storage_path('uploads'),
        ],
        //  hosted storage
        'hosted'  => [
            'driver' => 'local',
            'root'   => '/data/storage',
        ],
    ],
];
