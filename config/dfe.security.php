<?php
//******************************************************************************
//* DFE Ops Client Configuration
//******************************************************************************

return [
    'console-api-url'           => env( 'DFE_CONSOLE_API_URL' ),
    /** This key needs to match the key configured in the console */
    'console-api-key'           => env( 'DFE_CONSOLE_API_KEY' ),
    'console-api-client-id'     => env( 'DFE_CONSOLE_API_CLIENT_ID' ),
    'console-api-client-secret' => env( 'DFE_CONSOLE_API_CLIENT_SECRET' ),
];