<?php
//******************************************************************************
//* DFE Console Specific Settings
//******************************************************************************

return [
    'version'      => 'v1.0.0-alpha',
    'provisioning' => [
        //  Storage & storage layout options/settings
        'storage-zone-type'           => 'static',          //  Either "static" or "dynamic"
        'static-zone-name'            => 'ec2.us-east-1a',  //  The "static" storage zone
        'hosted-storage-base-path'    => '/data/storage',   //  absolute path to storage drive
        'local-storage-base-path'     => 'storage',         //  relative to installation
        'public-path-base'            => '/',               //  relative to storage path (hosted or non)
        'private-path-base'           => '.private',        //  relative to storage path (hosted or non)
        'public-paths'                => ['applications', 'plugins', '.private',],
        'private-paths'               => ['.cache', 'config', 'scripts', 'scripts.user',],
        //  Instance provisioning defaults
        'default-cluster-id'          => env( 'DFE_DEFAULT_CLUSTER', 'cluster-east-1' ),
        'default-guest-location'      => env( 'DFE_DEFAULT_GUEST_LOCATION', 1 ),
        'default-ram-size'            => env( 'DFE_DEFAULT_RAM_SIZE', 1 ),
        'default-disk-size'           => env( 'DFE_DEFAULT_DISK_SIZE', 8 ),
        //  Instance defaults
        //@todo update image to 14.* LTS x64
        'default-vendor-image-id'     => 4647,              //	Ubuntu server 12.04.1 i386
        'default-vendor-image-flavor' => 0,                 //	i386
        'default-dns-zone'            => env( 'DFE_DEFAULT_ZONE', 'cloud' ),
        'default-dns-domain'          => env( 'DFE_DEFAULT_DOMAIN', 'dreamfactory.com' ),
        //  Disallowed instance names
        'forbidden-names'             => [
            'dreamfactory',
            'dream',
            'factory',
            'developer',
            'wiki',
            'cloud',
            'www',
            'fabric',
            'api',
            'db',
            'database',
            'dsp',
            'dfe',
            'dfac',
            'df',
            'dfab',
            'dfdsp',
        ],
    ],
];