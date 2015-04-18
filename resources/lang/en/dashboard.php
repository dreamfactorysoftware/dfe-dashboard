<?php
/**
 * English - Dashboard strings
 */
return [
    //******************************************************************************
    //* Instance status
    //******************************************************************************
    'status-error'                => 'There was an error completing your request.',
    'status-starting'             => 'Your instance is starting up.',
    'status-stopping'             => 'Your instance is shutting down.',
    'status-terminating'          => 'Your instance is being terminated.',
    'status-up'                   => 'Your instance is up and running.',
    'status-down'                 => 'Your instance is shut down.',
    'status-dead'                 => 'Your instance is terminated.',
    'status-other'                => 'Your request is being processed.',
    //******************************************************************************
    //* Instance panel text
    //******************************************************************************
    'instance-name-label'         => 'Name',
    'instance-import-label'       => 'Choose',
    'instance-import-button-text' => 'Import',
    'instance-proof-text'         => 'This is just to prove you are actually an organic being.',
    'instance-create-heading'     => 'Create New',
    'instance-create-title'       => 'Create a New Instance',
    'instance-create-button-text' => 'Create',
    'instance-create-help'        => <<<HTML
<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">We will send you an email when your platform is ready.</p>
HTML
    ,
    'instance-import-heading'     => 'Import Existing',
    'instance-import-title'       => 'Have an existing snapshot?',
    //******************************************************************************
    //* Instance panel bodies
    //******************************************************************************
    'instance-default'            => null,
    'instance-create'             => <<<HTML
<p>Please choose a name for your new instance below. Once the creation process has completed, you will receive an email with access details.</p><p>Letters, numbers, and dashes are the only characters allowed.</p>
HTML
    ,
    'instance-import'             => <<<HTML
<p>Please choose a snapshot to import from the drop-down below. You may alternatively upload an existing snapshot by clicking the <strong>Upload Your Own</strong> button below. You will receive an email once the import is complete.</p>
<p class="help-block" style="margin-top:2px; font-size: 13px; color:#888;">Currently, only exports created by the DreamFactory Enterprise Dashboard are supported.</p>
HTML
    ,

];
