<?php
//******************************************************************************
//* DFE Ops Client Configuration
//******************************************************************************

use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

return [
    'console-api-url'           => env( 'DFE_CONSOLE_API_URL', 'http://dfe-console.local/api/v1/ops/' ),
    /** This key needs to match the key configured in the console */
    'console-api-key'           => env( 'DFE_CONSOLE_API_KEY', 'some-random-string' ),
    'console-api-client-id'     => env( 'DFE_CONSOLE_API_CLIENT_ID' ),
    'console-api-client-secret' => env( 'DFE_CONSOLE_API_CLIENT_SECRET' ),
    'signature-method'          => env( 'DFE_SIGNATURE_METHOD', DashboardDefaults::SIGNATURE_METHOD ),
    //******************************************************************************
    //* api overrides
    //******************************************************************************
    /** If true, uses below overrides instead of allowing console placement on guest */
    'override-cluster-servers'  => false,
    'override-cluster-id'       => false,
    'override-db-server-id'     => false,
    'override-app-server-id'    => false,
    'override-web-server-id'    => false,
];