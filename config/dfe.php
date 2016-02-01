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
        'display-name'       => 'DreamFactory Enterprise&trade; Dashboard',
        'display-version'    => env('DFE_VERSION', '1.0.8'),
        'display-copyright'  => '© DreamFactory Software, Inc. 2012-' . date('Y') . '. All Rights Reserved.',
        /**
         * Theme selection -- a bootswatch theme name
         * Included are cerulean, darkly, flatly, paper, and superhero.
         * You may also install other compatible themes and use them as well.
         */
        'themes'             => ['auth' => config('DFE_AUTH_THEME', 'darkly'), 'page' => config('DFE_PAGE_THEME', 'yeti')],
        /**
         * Auth pages 256x256px image
         * Shown on auth pages
         */
        'login-splash-image' => env('DFE_LOGIN_SPLASH_IMAGE', '/vendor/dfe-common/img/logo-dfe.png'),
        /**
         * NavBar 194x42px image
         * Shown on top of inner pages.
         */
        'navbar-image'       => env('DFE_NAVBAR_IMAGE', '/img/logo-navbar-194x42.png'),
        /** Custom css to load */
        'custom-css-file'    => env('DFE_CUSTOM_CSS_FILE'),
    ],
    'security'         => [
        /** This key needs to match the key configured in the console */
        'console-api-key'           => env('DFE_CONSOLE_API_KEY'),
        /** This is the full url to the DFE Console API endpoint */
        'console-api-url'           => env('DFE_CONSOLE_API_URL', config('DFE_DEFAULT_DOMAIN_PROTOCOL', 'https') . '://localhost/api/v1/ops/'),
        /** These keys are assigned during system installation */
        'console-api-client-id'     => env('DFE_CONSOLE_API_CLIENT_ID'),
        'console-api-client-secret' => env('DFE_CONSOLE_API_CLIENT_SECRET'),
        'guzzle-config'             => [],
    ],
];
