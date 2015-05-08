<?php
/**
 * Configuration file for the dfe-common library
 */
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

return [
    /** Global options */
    'display-name'    => 'Platform Dashboard',
    'display-version' => 'v1.0.x-alpha',
    'instance-prefix' => DashboardDefaults::INSTANCE_PREFIX,
    /** Theme selection (flatly or darkly) */
    'theme'           => 'flatly',
];