<?php
/**
 * Configuration file for the dfe-common library
 */
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

return [
    /** Global options */
    'display-name'      => 'Platform Dashboard',
    'display-version'   => 'v1.0.x-alpha',
    'instance-prefix'   => DashboardDefaults::INSTANCE_PREFIX,
    /** Theme selection (flatly or darkly) */
    'theme'             => 'flatly',
    /** Log locations */
    'log-path'          => env( 'DFE_LOG_PATH', '/data/logs/console' ),
    'log-file-name'     => 'laravel.log',
    /** Registration  */
    'registration-open' => false,
];
