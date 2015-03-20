<?php
use DreamFactory\Yii\Utility\Pii;

/**
 * aliases.config.php
 * A single location for all your aliasing needs!
 */

//	Already loaded? we done...
if ( false !== Yii::getPathOfAlias( 'DreamFactory.Yii.*' ) )
{
    return true;
}

$_aliasBasePath = dirname( __DIR__ );
$_aliasVendorPath = $_aliasBasePath . '/vendor';

Pii::setPathOfAlias( 'vendor', $_aliasVendorPath );

//	lib-php-common-yii (psr-0 && psr-4 compatible)
$_aliasLibPath =
    $_aliasVendorPath .
    '/dreamfactory/lib-php-common-yii' .
    ( is_dir( $_aliasVendorPath . '/dreamfactory/lib-php-common-yii/src' ) ? '/src' : '/DreamFactory/Yii' );

Pii::alias( 'DreamFactory.Yii', $_aliasLibPath );
Pii::alias( 'DreamFactory.Yii.Components', $_aliasLibPath . '/Components' );
Pii::alias( 'DreamFactory.Yii.Behaviors', $_aliasLibPath . '/Behaviors' );
Pii::alias( 'DreamFactory.Yii.Utility', $_aliasLibPath . '/Utility' );
Pii::alias( 'DreamFactory.Yii.Logging', $_aliasLibPath . '/Logging' );
Pii::alias( 'DreamFactory.Yii.Logging.LiveLogRoute', $_aliasLibPath . '/Logging/LiveLogRoute.php' );

//	lib-php-common-platform (psr-0 && psr-4 compatible)
$_aliasLibPath =
    $_aliasVendorPath .
    '/dreamfactory/lib-php-common-platform' .
    ( is_dir( $_aliasVendorPath . '/dreamfactory/lib-php-common-platform/src' ) ? '/src' : '/DreamFactory/Platform' );

Pii::alias( 'DreamFactory.Platform.Services', $_aliasLibPath . '/Services' );
Pii::alias( 'DreamFactory.Platform.Services.Portal', $_aliasLibPath . '/Services/Portal' );
Pii::alias( 'DreamFactory.Platform.Yii.Behaviors', $_aliasLibPath . '/Yii/Behaviors' );
Pii::alias( 'DreamFactory.Platform.Yii.Models', $_aliasLibPath . '/Yii/Models' );

//	Vendors
Pii::alias( 'Swift', $_aliasVendorPath . '/swiftmailer/swiftmailer/lib/classes' );

unset( $_aliasLibPath, $_aliasVendorPath, $_aliasBasePath );
