<?php
use DreamFactory\Yii\Utility\Pii;

/**
 * DreamFactory DSP Dashboard
 * Copyright 2012-2013 DreamFactory Software, Inc. <support@dreamfactory.com>
 */
/**
 * web.php
 * This is the main configuration file for the DreamFactory Services Platform server application.
 */

/**
 * Load up the database and common configurations between the web and background apps,
 * setting globals whilst at it.
 */
$_commonConfig = require( __DIR__ . '/common.config.php' );

//.........................................................................
//. The configuration himself (like Raab)
//.........................................................................

return array(
	/**
	 * Basics
	 */
	'basePath'          => $_appRoot,
	'name'              => $_appName,
	'runtimePath'       => $_logFilePath,
	'defaultController' => $_defaultController,
	/**
	 * Preloads
	 */
	'preload'           => array( 'log' ),
	/**
	 * Imports
	 */
	'import'            => array(
		'system.utils.*',
		'application.models.*',
		'application.models.forms.*',
		'application.components.*',
	),
	/**
	 * Components
	 */
	'components'        => array(
		//	Asset management
		'assetManager'     => array(
			'class'      => 'CAssetManager',
			'basePath'   => 'assets',
			'baseUrl'    => '/assets',
			'linkAssets' => true,
		),
		//.........................................................................
		//. Database Configurations. One connection per file
		//.........................................................................

		//	Database (Site/Main)
		'db'               => require( __DIR__ . '/database/fabric_auth.config.php' ),
		//	Database (Site/Main)
		'db.fabric_auth'   => require( __DIR__ . '/database/fabric_auth.config.php' ),
		//	Deployment Database
		'db.fabric_deploy' => require( __DIR__ . '/database/fabric_deploy.config.php' ),
		//	Fabric Host Master
		'db.cumulus'       => require( __DIR__ . '/database/cumulus.fabric.config.php' ),
		//	Error management
		'errorHandler'     => array(
			'errorAction' => $_defaultController . '/error',
		),
		//	Route configuration
		'urlManager'       => array(
			'caseSensitive'  => false,
			'urlFormat'      => 'path',
			'showScriptName' => false,
		),
		//	User configuration
		'user'             => array(
			'allowAutoLogin' => true,
			'loginUrl'       => array( $_defaultController . '/login' ),
		),
		'clientScript'     => array(
			'scriptMap' => array(
				'jquery.js'     => false,
				'jquery.min.js' => false,
			),
		),
		//	Logging configuration
		'log'              => array(
			'class'  => 'CLogRouter',
			'routes' => array(
				array(
					'class'       => 'DreamFactory\\Yii\\Logging\\LiveLogRoute',
					'maxFileSize' => '102400',
					'logFile'     => $_logFileName,
					'logPath'     => $_logFilePath,
//					'levels'      => 'error, warning, trace, info, debug, notice',
				),
				array(
					'class'         => 'CWebLogRoute',
					'categories'    => 'system.db.CDbCommand',
					'showInFireBug' => true,
				),
			),
		),
		//	Database Cache
		'cache'            => $_dbCache,
	),
	//.........................................................................
	//. Global application parameters
	//.........................................................................

	'params'            => $_commonConfig,
);
