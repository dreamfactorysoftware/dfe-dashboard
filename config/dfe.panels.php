<?php
//******************************************************************************
//* DFE Dashboard Panel Settings
//******************************************************************************

use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

return [
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
];