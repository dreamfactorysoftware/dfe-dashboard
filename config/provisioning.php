<?php
//******************************************************************************
//* DFE Dashboard provisioning defaults
//******************************************************************************

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;

return [
    //******************************************************************************
    //* Storage & storage layout options/settings
    //******************************************************************************
    //  The root path of where instances' data will live
    'storage-root'                => env('DFE_HOSTED_BASE_PATH', storage_path()),
    //  Either "static" or "dynamic"
    'storage-zone-type'           => env('DFE_STORAGE_ZONE_TYPE', 'static'),
    //  The "static" storage zone
    'static-zone-name'            => env('DFE_STATIC_ZONE_NAME', 'ec2.us-east-1a'),
    //  relative to storage path (hosted or non)
    'public-path-base'            => env('DFE_PUBLIC_PATH_BASE', '/'),
    //  relative to storage path (hosted or non)
    'private-path-name'           => env('DFE_PRIVATE_PATH_NAME', EnterpriseDefaults::PRIVATE_PATH_NAME),
    // relative to owner-private-path
    'snapshot-path-name'          => env('DFE_SNAPSHOT_PATH_NAME', EnterpriseDefaults::SNAPSHOT_PATH_NAME),
    /** "DFE_*_PATHS" variables can contain one or more, pipe-delimited names of directories to create */
    'public-paths'                => explode('|',
        env('DFE_PUBLIC_PATHS', EnterpriseDefaults::DEFAULT_REQUIRED_STORAGE_PATHS)),
    'private-paths'               => explode('|',
        env('DFE_PRIVATE_PATHS', EnterpriseDefaults::DEFAULT_REQUIRED_PRIVATE_PATHS)),
    'owner-private-paths'         => explode('|',
        env('DFE_OWNER_PRIVATE_PATHS', EnterpriseDefaults::DEFAULT_REQUIRED_OWNER_PRIVATE_PATHS)),
    //******************************************************************************
    //* Instance provisioning defaults
    //******************************************************************************
    'default-cluster-id'          => env('DFE_DEFAULT_CLUSTER'),
    'default-db-server-id'        => env('DFE_DEFAULT_DATABASE'),
    'default-guest-location'      => env('DFE_DEFAULT_GUEST_LOCATION', GuestLocations::DFE_CLUSTER),
    'default-ram-size'            => env('DFE_DEFAULT_RAM_SIZE', 1),
    'default-disk-size'           => env('DFE_DEFAULT_DISK_SIZE', 8),
    //  Notification settings
    'email-subject-prefix'        => env('DFE_EMAIL_SUBJECT_PREFIX', '[DFE]'),
    //  Instance defaults
    'default-dns-zone'            => env('DFE_DEFAULT_DNS_ZONE'),
    'default-dns-domain'          => env('DFE_DEFAULT_DNS_DOMAIN'),
    'default-domain'              => env('DFE_DEFAULT_DOMAIN'),
    'default-domain-protocol'     => env('DFE_DEFAULT_DOMAIN_PROTOCOL', EnterpriseDefaults::DEFAULT_DOMAIN_PROTOCOL),
    //@todo update image to 14.* LTS x64
    //  Ubuntu server 12.04.1 i386
    'default-vendor-image-id'     => 4647,
    //	i386
    'default-vendor-image-flavor' => 0,
];
