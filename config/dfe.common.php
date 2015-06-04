<?php
/**
 * Configuration file for the dfe-common library
 */

return [
    //******************************************************************************
    //* Global Options
    //******************************************************************************
    'display-name'         => 'DreamFactory Enterprise&trade; Dashboard',
    'display-version'      => 'v1.0.x-alpha',
    'display-copyright'    => '© DreamFactory Software, Inc. 2012-' . date( 'Y' ) . '. All Rights Reserved.',
    /**
     * Theme selection -- a bootswatch theme name
     * Included are cerulean, darkly, flatly, paper, and superhero.
     * You may also install other compatible themes and use them as well.
     */
    'themes'               => ['auth' => 'darkly', 'page' => 'flatly'],
    //******************************************************************************
    //* Custom Logging
    //******************************************************************************
    /** If these two are specified, log-path and log-file-name will be used instead of the default "storage/logs/laravel.log" */
    'old-log-config-class' => 'Illuminate\\Foundation\\Bootstrap\\ConfigureLogging',
    'new-log-config-class' => 'DreamFactory\\Enterprise\\Common\\Bootstrap\\CommonLoggingConfiguration',
    /** The log path and file name to use if the above two are specified */
    'log-path'             => env( 'DFE_LOG_PATH', '/data/logs/dashboard' ),
    'log-file-name'        => env( 'DFE_LOG_FILE_NAME', 'laravel.log' ),
];
