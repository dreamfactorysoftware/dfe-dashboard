<?php
/**
 * English - Dashboard strings
 */
return [
    //******************************************************************************
    //* Operation results (flash alert settings)
    //******************************************************************************
    'success'                     => ['title' => 'Success',],
    'failure'                     => ['title' => 'Failure',],
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
    'instance-name-label'         => 'New Instance Name',
    'instance-id-label'           => 'Imported Instance Name',
    'instance-or-label'           => 'or',
    'instance-import-label'       => 'Restore Instance',
    'instance-import-button-text' => 'Restore',
    'instance-upload-label'       => 'Upload Import',
    'instance-upload-button-text' => 'Upload',
    'instance-proof-text'         => 'I am not a robot.',
    'instance-create-heading'     => 'Create New',
    'instance-create-title'       => 'Create a New Instance',
    'instance-create-button-text' => 'Create',
    'instance-create-help'        => '<p class="help-block">Instance names may contain only letters, numbers, and underscores.</p>',
    'instance-create'             => '<p>Please choose a name for your new instance below. Once the creation process has completed, you will receive an email with access details.</p>',
    'instance-import-heading'     => 'Import Existing',
    'instance-import-title'       => 'Have an existing snapshot?',
    'instance-upload-heading'     => 'Upload Your Own',
    'instance-upload-title'       => 'Have an export from another system?',
    //******************************************************************************
    //* Instance panel bodies
    //******************************************************************************
    'instance-default'            => null,
    'instance-import'             => <<<HTML
<p>Please choose a snapshot to import from the drop-down below. You will receive an email once the process completes.</p>
<p class="help-block" style="margin-top:2px; font-size: 13px; color:#888;">Currently, only exports created by this dashboard are supported.</p>
HTML

    ,
    'instance-import-help'        => '<p class="help-block">Select an export to restore.</p>',
    'instance-upload-help'        => '<p class="help-block">Select an existing export file (*.zip or *.tar.gz) to upload from your system and click the <strong>Upload</strong> button.</p>',
    //******************************************************************************
    //* Instance operational messages
    //******************************************************************************
    'export-success'              => 'Your export is queued and you will receive an email with instructions for access.',
    'export-failure'              => 'Your export request failed. Please try again later.',
    //******************************************************************************
    //* Others
    //******************************************************************************
    'session-expired'             => 'Your session has expired or is otherwise not valid.',
];
