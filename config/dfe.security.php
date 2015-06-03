<?php
//******************************************************************************
//* DFE Ops Client Configuration
//******************************************************************************

return [
    'console-api-url'           => env( 'DFE_CONSOLE_API_URL', 'http://dfe-console.local/api/v1/ops/' ),
    /** This key needs to match the key configured in the console */
    'console-api-key'           => env( 'DFE_CONSOLE_API_KEY', 'some-random-string' ),
    'console-api-client-id'     => env( 'DFE_CONSOLE_API_CLIENT_ID' ),
    'console-api-client-secret' => env( 'DFE_CONSOLE_API_CLIENT_SECRET' ),
];