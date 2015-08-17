<?php
//******************************************************************************
//* DFE General Settings
//******************************************************************************

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;

return [
    //******************************************************************************
    //* General
    //******************************************************************************
    //  The id of THIS cluster
    'cluster-id'       => env('DFE_CLUSTER_ID'),
    //  A string to be pre-pended to instance names for non-admin users
    'instance-prefix'  => env('DFE_DEFAULT_INSTANCE_PREFIX'),
    //  This is the algorithm used for signing API transactions. Defaults to 'sha256'
    'signature-method' => env('DFE_SIGNATURE_METHOD', EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD),
    //  The name of the site partner, if any.
    'partner'          => env('DFE_PARTNER_ID'),
    //******************************************************************************
    //* Common across all DFE apps
    //******************************************************************************
    'common'           => [
        //******************************************************************************
        //* Global Options
        //******************************************************************************
        'display-name'      => 'DreamFactory Enterprise&trade; Dashboard',
        'display-version'   => 'v1.0-beta',
        'display-copyright' => '© DreamFactory Software, Inc. 2012-' . date('Y') . '. All Rights Reserved.',
        /**
         * Theme selection -- a bootswatch theme name
         * Included are cerulean, darkly, flatly, paper, and superhero.
         * You may also install other compatible themes and use them as well.
         */
        'themes'            => ['auth' => 'darkly', 'page' => 'yeti'],
    ],
    'security'         => [
        /** This key needs to match the key configured in the console */
        'console-api-key'           => env('DFE_CONSOLE_API_KEY'),
        /** This is the full url to the DFE Console API endpoint */
        'console-api-url'           => env('DFE_CONSOLE_API_URL', 'http://localhost/api/v1/ops/'),
        /** These keys are assigned during system installation */
        'console-api-client-id'     => env('DFE_CONSOLE_API_CLIENT_ID'),
        'console-api-client-secret' => env('DFE_CONSOLE_API_CLIENT_SECRET'),
        'guzzle-config'             => [],
    ],
];
