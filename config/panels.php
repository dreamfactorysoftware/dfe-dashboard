<?php
//******************************************************************************
//* Dashboard UI Layout/Panel Settings
//******************************************************************************
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

return [
    /** If true, uses below overrides instead of allowing console placement on guest */
    'panels-per-row'    => DashboardDefaults::PANELS_PER_ROW,
    'columns-per-panel' => DashboardDefaults::COLUMNS_PER_PANEL,
    'toolbar-size'      => 'btn-group-xs',
    /** This is the create from scratch panel **/
    'create'            => [
        'context'          => 'panel-warning',
        'template'         => DashboardDefaults::CREATE_INSTANCE_BLADE,
        'header-icon'      => null,
        'header-icon-size' => 'fa-1x',
        'body-icon'        => false,
        'description'      => 'common.instance-create',
        'form-id'          => 'form-create',
    ],
    /** This is the create via import panel **/
    'import'            => [
        'context'          => 'panel-warning',
        'template'         => DashboardDefaults::IMPORT_INSTANCE_BLADE,
        'header-icon'      => 'fa-cloud-upload',
        'header-icon-size' => 'fa-1x',
        'body-icon'        => false,
        'description'      => 'common.instance-import',
        'form-id'          => 'form-import',
    ],
    /** This is the default panel for an existing instance **/
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
        'description'             => 'common.instance-default',
        'form-id'                 => 'form-default',
    ],
];
