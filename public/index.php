<?php
/**
 * index.php
 * Main entry point/bootstrap for all processes
 *
 * This file is subject to the terms and conditions defined in the file 'LICENSE.txt' that is part of this repository.
 */
/**
 * @type bool Set to TRUE to enable debug logging
 */
const ENABLE_DEBUG_MODE = false;

//	Load up composer...
$_basePath = dirname( __DIR__ );
$_autoloader = require_once( $_basePath . '/vendor/autoload.php' );

//	Load up framework
require_once $_basePath . '/vendor/dreamfactory/yii/framework/yiilite.php';

if ( ENABLE_DEBUG_MODE )
{
    ini_set( 'display_errors', 1 );

    defined( 'YII_DEBUG' ) or define( 'YII_DEBUG', true );
    defined( 'YII_TRACE_LEVEL' ) or define( 'YII_TRACE_LEVEL', 3 );
}

use DreamFactory\Yii\Utility\Pii;

//	Create the application and run
Pii::run( __DIR__, $_autoloader );
