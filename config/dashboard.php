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
    'view-path'               => base_path() . '/resources/views',
    /** If true, recaptcha is required on new hosted instances */
    'require-captcha'         => false,
    /** The prefix for all non-admin provisioned instances  */
    'instance-prefix'         => DashboardDefaults::INSTANCE_PREFIX,
    //  Instance defaults
    'default-dns-zone'        => env( 'DFE_DEFAULT_DNS_ZONE', 'enterprise' ),
    'default-dns-domain'      => env( 'DFE_DEFAULT_DNS_DOMAIN', 'dreamfactory.com' ),
    'default-domain'          => env( 'DFE_DEFAULT_DOMAIN', 'dreamfactory.com' ),
    'default-domain-protocol' => 'https',
    /** Provisioners configured on this dashboard */
    'provisioners'            => ['rave',],
    //******************************************************************************
    //* ui/ux settings
    //******************************************************************************
    'panels'                  => [
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
    'panel-context'           => 'panel-info',
    'create-panel-context'    => 'panel-success',
    'import-panel-context'    => 'panel-warning',
    'help-button-url'         => 'http://www.dreamfactory.com/',
    /** FontAwesome icons to use */
    'icons'                   => [
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
];