<?php
/**
 * English - Dashboard strings
 */
return [
    //******************************************************************************
    //* Operation results (flash alert settings)
    //******************************************************************************
    'success'                             => ['title' => 'Success',],
    'failure'                             => ['title' => 'Failure',],
    //******************************************************************************
    //* Instance status
    //******************************************************************************
    'status-error'                        => 'There was an error completing your request.',
    'status-starting'                     => 'Your instance is starting up.',
    'status-stopping'                     => 'Your instance is shutting down.',
    'status-terminating'                  => 'Your instance is being terminated.',
    'status-up'                           => 'Your instance is up and running.',
    'status-down'                         => 'Your instance is shut down.',
    'status-dead'                         => 'Your instance is terminated.',
    'status-other'                        => 'Your request is being processed.',
    //******************************************************************************
    //* Instance panel text
    //******************************************************************************
    'instance-name-label'                 => 'New Instance Name',
    'instance-id-label'                   => 'New Instance Name',
    'instance-or-label'                   => 'or',
    'instance-package-empty-label'        => 'No existing snapshots',
    'instance-package-label'              => 'Package File<br/><span class="text-muted"><small>optional</small></span>',
    'instance-proof-text'                 => 'I am not a robot.',
    //******************************************************************************
    //* Instance panel bodies
    //******************************************************************************
    'instance-default'                    => null,
    'instance-import'                     => <<<HTML
<p>Please choose a snapshot to import from the drop-down below. You will receive an email once the process completes.</p>
<p class="help-block" style="margin-top:2px; font-size: 13px; color:#888;">Currently, only exports created by this dashboard are supported.</p>
HTML

    ,
    'instance-package-help'               => '<p class="help-block">You may optionally choose a DreamFactory&trade; package file and have it installed on your instance automatically.</p>',
    //******************************************************************************
    //* Tabs
    //******************************************************************************
    'tab-names'                           => [
        'new'     => 'Create an Instance',
        'restore' => 'Import a Snapshot',
    ],
    //******************************************************************************
    //* Create
    //******************************************************************************
    'instance-create-title'               => '<h4>Welcome to your DreamFactory Dashboard!</h4><Hp>Use the Dashboard to create and manage all of your DreamFactory instances in one place.</h4>',
    'instance-create-help'                => '<p class="help-block">Instance names may contain only letters, numbers, and underscores.</p>',
    'instance-create'                     => '<p>Please choose a name for your new instance below. Once the creation process has completed, you will receive an email with access details.</p>',
    //******************************************************************************
    //* Restore/Importing
    //******************************************************************************
    'instance-import-title'               => 'Have an existing snapshot?',
    'instance-import-help'                => '<p class="help-block">Select an export to restore.</p>',
    'instance-import-label'               => 'Choose a Snapshot',
    'instance-import-select-label'        => 'Select a Snapshot...',
    'instance-import-empty-label'         => 'No existing snapshots',
    /** Uploaded file */
    'instance-upload-heading'             => 'Upload Your Own',
    'instance-upload-title'               => 'Have a snapshot from another system?',
    'instance-upload-label'               => 'Browse for a Snapshot',
    'instance-upload-help'                => '<p class="help-block">Select an existing snapshot file (*.zip or *.tar.gz) to upload from your system and click the <strong>Upload</strong> button.</p>',
    //******************************************************************************
    //* Buttons
    //******************************************************************************
    'instance-create-button-text'         => 'Create Instance',
    'instance-upload-button-text'         => 'Upload and Create',
    'instance-import-button-text'         => 'Import Snapshot',
    'instance-upload-restore-button-text' => 'Upload Snapshot',
    //******************************************************************************
    //* Instance operational messages
    //******************************************************************************
    'export-success'                      => 'Your snapshot is queued and you will receive an email with instructions for access.',
    'export-failure'                      => 'Your snapshot request failed. Please try again later.',
    //******************************************************************************
    //* Others
    //******************************************************************************
    'session-expired'                     => 'Your session has expired or is otherwise not valid.',
    'import-staged'                       => 'Ready for Import.',
];
