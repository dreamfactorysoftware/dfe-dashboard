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
    'instance-prefix'          => DashboardDefaults::INSTANCE_PREFIX,
    //******************************************************************************
    //* ui/ux settings
    //******************************************************************************
    //  Instance defaults
    'default-dns-zone'         => env( 'DFE_DEFAULT_ZONE', 'enterprise' ),
    'default-dns-domain'       => env( 'DFE_DEFAULT_DOMAIN', 'dreamfactory.com' ),
    'default-domain-protocol'  => 'https',
    'panels'                   => [
        'panels-per-row'    => DashboardDefaults::PANELS_PER_ROW,
        'columns-per-panel' => DashboardDefaults::COLUMNS_PER_PANEL,
        'toolbar-size'      => 'btn-group-xs',
        'create'            => [
            'context'          => 'panel-success',
            'template'         => DashboardDefaults::CREATE_INSTANCE_BLADE,
            'header-icon'      => 'fa-asterisk',
            'header-icon-size' => 'fa-1x',
            'body-icon'        => false,
            'description'      => 'dashboard.instance-create',
            'form-id'          => 'form-create',
        ],
        'import'            => [
            'context'          => 'panel-warning',
            'template'         => DashboardDefaults::IMPORT_INSTANCE_BLADE,
            'header-icon'      => 'fa-cloud-upload',
            'header-icon-size' => 'fa-1x',
            'body-icon'        => false,
            'description'      => 'dashboard.instance-import',
            'form-id'          => 'form-import',
        ],
        'default'           => [
            'context'                 => DashboardDefaults::PANEL_CONTEXT,
            'template'                => DashboardDefaults::DEFAULT_INSTANCE_BLADE,
            'header-status-icon'      => 'fa-thumbs-o-up',
            'header-status-icon-size' => 'fa-1x',
            'status-icon'             => 'fa-thumbs-o-up',
            'status-icon-size'        => 'fa-2x',
            'status-icon-context'     => 'text-success',
            'help-icon'               => 'fa-question',
            'help-url'                => 'http://www.dreamfactory.com/resources/',
            'help-text'               => null,
            'description'             => 'dashboard.instance-default',
            'form-id'                 => 'form-default',
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
    'api-client-id'            => 'acbab38ec7c7f9eeb97ec957b53857050d8b3b7b753b95ffb31e7161140049ea',
    'api-client-secret'        => '97b61eb7ad89bb63b6c575a90ffb86f971a7f0914210f84dcc827cd54fac4f27',
    'client-hash-key'          => '%]3,]~&t,EOxL30[wKw3auju:[+L>eYEVWEP,@3n79Qy',
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
        'hel'         => 'fa-question',
        'launch'      => 'fa-rocket',
        'create'      => 'fa-rocket',
        'start'       => 'fa-play',
        'stop'        => 'fa-stop',
    ],
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