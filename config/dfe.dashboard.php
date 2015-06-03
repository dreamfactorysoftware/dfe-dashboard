<?php
//******************************************************************************
//* DFE Dashboard Settings
//******************************************************************************

return [
    //******************************************************************************
    //* general dashboard settings
    //******************************************************************************
    /** The path to our views */
    'view-path'                => base_path() . '/resources/views',
    /** If true, recaptcha is required on new hosted instances */
    'require-captcha'          => false,
    //  Instance defaults
    'default-dns-zone'         => env( 'DFE_DEFAULT_DNS_ZONE', 'enterprise' ),
    'default-dns-domain'       => env( 'DFE_DEFAULT_DNS_DOMAIN', 'dreamfactory.com' ),
    'default-domain'           => env( 'DFE_DEFAULT_DOMAIN', 'dreamfactory.com' ),
    'default-domain-protocol'  => 'https',
    //  UI defaults
    'panel-context'            => 'panel-info',
    'create-panel-context'     => 'panel-success',
    'import-panel-context'     => 'panel-warning',
    'help-button-url'          => 'http://www.dreamfactory.com/',
    /** If true, uses below overrides instead of allowing console placement on guest */
    'override-cluster-servers' => false,
    'override-cluster-id'      => false,
    'override-db-server-id'    => false,
    'override-app-server-id'   => false,
    'override-web-server-id'   => false,
];
