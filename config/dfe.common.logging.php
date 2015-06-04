<?php
/**
 * Configuration file for dfe-common common logging
 */

return [
    //******************************************************************************
    //* Common Logging Settings
    //******************************************************************************
    /** If these two are specified, log-path and log-file-name will be used instead of the default "storage/logs/laravel.log" */
    'old-log-config-class' => 'Illuminate\\Foundation\\Bootstrap\\ConfigureLogging',
    'new-log-config-class' => 'DreamFactory\\Enterprise\\Common\\Bootstrap\\ConfigureCommonLogging',
    /** The log path and file name to use if the above two are specified */
    'log-path'             => env( 'DFE_LOG_PATH' ),
    'log-file-name'        => env( 'DFE_LOG_FILE_NAME' ),
];
