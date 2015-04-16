<?php
//******************************************************************************
//* DFE Dashboard Specific Settings
//******************************************************************************

use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

return [
    //******************************************************************************
    //* general dashboard settings
    //******************************************************************************
    /** The path to our views */
    'view-path'                => base_path() . '/resources/views',
    /** If true, recaptcha is required on new hosted instances */
    'require-captcha'          => false,
    /** The prefix for all non-admin provisioned instances  */
    'instance-prefix'          => 'dsp-',
    //******************************************************************************
    //* ui/ux settings
    //******************************************************************************
    'default-domain'           => env( 'DFE_DEFAULT_DOMAIN', '.cloud.dreamfactory.com' ),
    'default-domain-protocol'  => 'https',
    'columns-per-panel'        => DashboardDefaults::COLUMNS_PER_PANEL,
    'panels'                   => [
        'create'   => [
            'context'     => 'panel-success',
            'template'    => 'layouts.create-instance',
            'header-icon' => 'fa-asterisk',
            'body-icon'   => false,
        ],
        'import'   => [
            'context'     => 'panel-warning',
            'template'    => 'layouts.import-instance',
            'header-icon' => 'fa-cloud-upload',
            'body-icon'   => false,
        ],
        'instance' => [
            'context'     => 'panel-info',
            'template'    => 'layouts.single-instance',
            'header-icon' => 'fa-thumbs-o-up',
            'body-icon'   => 'fa-thumbs-o-up',
            'help-icon'   => 'fa-question',
            'help-url'    => 'http://www.dreamfactory.com/resources/',
            'help-text'   => null,
        ],
    ],
    'panel-context'            => 'panel-info',
    'create-panel-context'     => 'panel-success',
    'import-panel-context'     => 'panel-warning',
    'help-button-url'          => 'http://www.dreamfactory.com/',
    //******************************************************************************
    //* console api settings
    //******************************************************************************
    'api-host'                 => 'http://dfe-console.local',
    'api-endpoint'             => '/api/v1',
    'hash-key'                 => '%]3,]~&t,EOxL30[wKw3auju:[+L>eYEVWEP,@3n79Qy',
    'client-app-key-id'        => 1,
    'client-id'                => 'acbab38ec7c7f9eeb97ec957b53857050d8b3b7b753b95ffb31e7161140049ea',
    'client-secret'            => '97b61eb7ad89bb63b6c575a90ffb86f971a7f0914210f84dcc827cd54fac4f27',
    /** Provisioners configured on this dashboard */
    'provisioners'             => ['rave',],
    /** FontAwesome icons to use */
    'icons'                    => [
        'import'      => 'fa-cloud-upload',
        'export'      => 'fa-cloud-download',
        'spinner'     => 'fa fa-spinner fa-spin text-info',
        'up'          => 'fa-thumbs-o-up',
        'down'        => 'fa-thumbs-o-down',
        'starting'    => 'fa fa-spinner fa-spin text-success',
        'stopping'    => 'fa fa-spinner fa-spin text-warning',
        'terminating' => 'fa fa-spinner fa-spin text-danger',
        'dead'        => 'fa-ambulance',
        'unknown'     => 'fa-question',
    ],
    /** Templates */
    'instance-panel-blade'     => DashboardDefaults::INSTANCE_PANEL_TEMPLATE,
    //******************************************************************************
    //* DFE console api overrides
    //******************************************************************************
    /** If true, uses below overrides instead of allowing console placement on guest */
    'override-cluster-servers' => false,
    'override-cluster-id'      => false,
    'override-db-server-id'    => false,
    'override-app-server-id'   => false,
    'override-web-server-id'   => false,
];