<?php
/**
 * DreamFactory DSP Dashboard
 * Copyright 2012-2014 DreamFactory Software, Inc. <support@dreamfactory.com>
 */
/**
 * common.config.php
 * This file contains any application-level parameters that are to be shared between the background and web services
 */
/**
 * @var string
 */
const ALIASES_CONFIG_PATH = '/aliases.config.php';
/**
 * @var string
 */
const DEFAULT_CLOUD_API_ENDPOINT = 'https://api.cloud.dreamfactory.com';
/**
 * @var string
 */
const DEFAULT_SUPPORT_EMAIL = 'support@dreamfactory.com';

//*************************************************************************
//* Global Configuration Settings
//*************************************************************************

//	The base path of the project, where it's checked out basically
$_basePath = dirname( __DIR__ );
//	The document root
$_docRoot = $_basePath . '/public';
//	The document root
$_appRoot = $_basePath . '/app';
//	The vendor path
$_vendorPath = $_basePath . '/vendor';
//	Set to false to disable database caching
$_dbCacheEnabled = true;
//	The name of the default controller. "site" just sucks
$_defaultController = 'web';
//	Where the log files go and the name...
$_logFilePath = $_basePath . '/log';
$_logFileName = basename( \Kisma::get( 'app.log_file' ) );

/**
 * Aliases
 */
/** @noinspection PhpIncludeInspection */
file_exists( __DIR__ . ALIASES_CONFIG_PATH ) && require( __DIR__ . ALIASES_CONFIG_PATH );

/**
 * Application Paths
 */
\Kisma::set( 'app.app_name', $_appName = 'DreamFactory DSP Dashboard' );
\Kisma::set( 'app.app_root', $_appRoot );
\Kisma::set( 'app.doc_root', $_docRoot );
\Kisma::set( 'app.log_path', $_logFilePath );
\Kisma::set( 'app.vendor_path', $_vendorPath );
\Kisma::set( 'app.log_file_name', $_logFileName );
\Kisma::set( 'app.project_root', $_basePath );

/**
 * Database Caching
 */
$_dbCache = $_dbCacheEnabled ? array(
    'class'                => 'CDbCache',
    'connectionID'         => 'db',
    'cacheTableName'       => 'df_sys_cache',
    'autoCreateCacheTable' => true,
) : null;

/**
 * Set up and return the common settings...
 */
return array(
    'base_path'                    => $_basePath,
    'api.endpoint'                 => DEFAULT_CLOUD_API_ENDPOINT,
    'oauth.salt'                   => 'rW64wRUk6Ocs+5c7JwQ{69U{]MBdIHqmx9Wj,=C%S#cA%+?!cJMbaQ+juMjHeEx[dlSe%h%kcI',
    /** Oasys options */
    'oasys.'                       => array(),
    /** Dashboard Settings */
    'dashboard.default_dsp_domain' => '.cloud.dreamfactory.com',
    'dashboard.cerberus_token'     => 'f4f6c99f8fb6bae9618a9217aaaaaafc',
    'dashboard.default_theme'      => 'default',
    //	Amazon
    'dashboard.amazon_ec2'         => array(
        'enabled'    => true,
        'ami'        => 'varies',
        'url'        => 'https://bitnami.com/stack/dreamfactory/cloud/amazon',
        'launch_url' => 'https://bitnami.com/stack/dreamfactory/cloud/amazon',
        'wiki_url'   => 'https://bitnami.com/stack/dreamfactory/cloud/README.txt',
    ),
    //	Azure
    'dashboard.azure'              => array(
        'enabled'    => true,
        'url'        => 'https://bitnami.com/stack/dreamfactory/cloud/azure',
        'launch_url' => 'https://bitnami.com/stack/dreamfactory/cloud/azure',
        'wiki_url'   => 'https://bitnami.com/stack/dreamfactory/cloud/README.txt',
    ),
    //	BitNami
    'dashboard.bitnami'            => array(
        'enabled'    => true,
        'url'        => 'https://bitnami.com/stack/dreamfactory',
        'launch_url' => 'https://bitnami.com/stack/dreamfactory',
        'wiki_url'   => 'https://bitnami.com/stack/dreamfactory/README.txt',
    ),
    //	VMWare
    'dashboard.vmware'             => array(
        'enabled'    => false,
        'url'        => 'https://solutionexchange.vmware.com/store/products/bitnami-dreamfactory-stack-1-1-3-0-ubuntu-12-04#.UqXzm3mJAw8',
        'launch_url' => 'https://solutionexchange.vmware.com/store/products/bitnami-dreamfactory-stack-1-1-3-0-ubuntu-12-04#.UqXzm3mJAw8',
        'wiki_url'   => 'https://solutionexchange.vmware.com/store/products/bitnami-dreamfactory-stack-1-1-3-0-ubuntu-12-04#.UqXzm3mJAw8',
    ),
    //	Rackspace
    'dashboard.rackspace'          => array(
        'enabled' => false,
    ),
    //******************************************************************************
    //* reCaptcha Settings
    //******************************************************************************

    'recaptcha.public_key'         => '6LeVovsSAAAAAAn5kDdQ-rElUMBzaf90tRe6DY3h',
    'recaptcha.private_key'        => '6LeVovsSAAAAAEhNYhsjet1EyKS7KePIbkyh0Xu0',
    'recaptcha.theme'              => 'white',
    'recaptcha.options'            => array(
        'lang'     => 'en',
        'ssl'      => false,
    ),
);
