<?php
//******************************************************************************
//* DFE Dashboard Specific Settings
//******************************************************************************
return [
    'require-captcha'         => false,
    'help-button-url'         => 'http://www.dreamfactory.com/',
    'default-domain'          => '.cloud.dreamfactory.com',
    'default-domain-protocol' => 'https',
    'api-host'                => 'http://dfe-console.local',
    'api-endpoint'            => '/api/v1',
    'hash-key'                => '%]3,]~&t,EOxL30[wKw3auju:[+L>eYEVWEP,@3n79Qy',
    'client-app-key-id'       => 1,
    'client-id'               => 'acbab38ec7c7f9eeb97ec957b53857050d8b3b7b753b95ffb31e7161140049ea',
    'client-secret'           => '97b61eb7ad89bb63b6c575a90ffb86f971a7f0914210f84dcc827cd54fac4f27',
    'cluster-id'              => 'cluster-east-1',
    'db-server-id'            => 'db-east-1',
    'provisioners'            => [
        'rave',
    ],
    'view-path'               => base_path() . '/resources/views',
    'panel-context'           => 'info',
];