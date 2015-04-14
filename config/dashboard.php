<?php
//******************************************************************************
//* DFE Dashboard Specific Settings
//******************************************************************************
return [
    //******************************************************************************
    //* General
    //******************************************************************************
    /** The path to our views */
    'view-path'                => base_path() . '/resources/views',
    /** If true, recaptcha is required on new hosted instances */
    'require-captcha'          => false,
    /** Dashboard UI info and settings */
    'panel-context'            => 'info',
    'create-panel-context'     => 'success',
    'import-panel-context'     => 'warning',
    'import-panel-icon'        => 'upload',
    'help-button-url'          => 'http://www.dreamfactory.com/',
    'default-domain'           => '.cloud.dreamfactory.com',
    'default-domain-protocol'  => 'https',
    /** The console api settings */
    'api-host'                 => 'http://dfe-console.local',
    'api-endpoint'             => '/api/v1',
    'hash-key'                 => '%]3,]~&t,EOxL30[wKw3auju:[+L>eYEVWEP,@3n79Qy',
    'client-app-key-id'        => 1,
    'client-id'                => 'acbab38ec7c7f9eeb97ec957b53857050d8b3b7b753b95ffb31e7161140049ea',
    'client-secret'            => '97b61eb7ad89bb63b6c575a90ffb86f971a7f0914210f84dcc827cd54fac4f27',
    /** Provisioners configured on this dashboard */
    'provisioners'             => [
        'rave',
    ],
    /** FontAwesome icons to use */
    'icons'                    => [
        'instance-up'   => 'fa-thumbs-o-up',
        'instance-down' => 'fa-thumbs-o-down',
        'instance-dead' => 'fa-ambulance',
    ],
    'instances-per-row'        => 4,
    'new-instance-html'        => '<p>Please enter the name of the new instance below, or you may keep the name we have selected for you. Letters, numbers, and dashes are the only characters allowed.</p>',
    'import-instance-html'     => '<p>Please choose a snapshot to import from the drop-down below. You may alternatively upload one through your browser by clicking the <strong>Upload</strong> button below the drop-down.</p><p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">Currently, only exports created by the DreamFactory Enterprise Dashboard are supported.</p>',
    //******************************************************************************
    //* DFE console api overrides
    //******************************************************************************
    /** If true, uses below overrides instead of allowing console placement on guest */
    'override-cluster-servers' => false,
    'override-cluster-id'      => false,
    'override-db-server-id'    => false,
    'override-app-server-id'   => false,
    'override-web-server-id'   => false,
];