<?php
//******************************************************************************
//* Dashboard specific settings
//******************************************************************************
use DreamFactory\Enterprise\Common\Enums\ServerTypes;

return [
    //******************************************************************************
    //* general dashboard settings
    //******************************************************************************
    /** The path to our views */
    'view-path'                => base_path() . '/resources/views',
    /** If true, recaptcha is required on new hosted instances */
    'require-captcha'          => env('DFE_REQUIRE_RECAPTCHA_FOR_LAUNCH', false),
    /** If true, users are allowed to upload their own exports for importing. Defaults to "false" */
    'allow-import-uploads'     => env('DFE_ALLOW_IMPORT_UPLOADS', false),
    //  Instance defaults
    'default-dns-zone'         => env('DFE_DEFAULT_DNS_ZONE', 'enterprise'),
    'default-dns-domain'       => env('DFE_DEFAULT_DNS_DOMAIN', 'dreamfactory.com'),
    'default-domain'           => env('DFE_DEFAULT_DOMAIN', 'dreamfactory.com'),
    'default-domain-protocol'  => env('DFE_DEFAULT_DOMAIN_PROTOCOL', 'https'),
    //  UI defaults
    'panel-context'            => 'panel-info',
    'create-panel-context'     => 'panel-success',
    'import-panel-context'     => 'panel-warning',
    'help-button-url'          => env('DFE_HELP_BUTTON_URL', 'https://www.dreamfactory.com/'),
    /** If true, uses below settings to override console default guest placements */
    'override-cluster-servers' => false,
    'override-cluster-id'      => false,
    'override-db-server-id'    => false,
    'override-app-server-id'   => false,
    'override-web-server-id'   => false,
    'button-contexts'          => [
        ServerTypes::DB  => 'primary',
        ServerTypes::WEB => 'success',
        ServerTypes::APP => 'warning',
    ],
    'upload-store'             => 'uploads',
    //******************************************************************************
    //* General UI Alert Settings
    //******************************************************************************
    'alerts'                   => [
        'success' => ['context' => 'alert-success',],
        'failure' => ['context' => 'alert-danger',],
    ],
];
