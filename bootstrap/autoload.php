<?php
define( 'LARAVEL_START', microtime( true ) );

//  Require the autoloader
require __DIR__ . '/../vendor/autoload.php';

//  And pre-compiled junk
$compiledPath = __DIR__ . '/../vendor/compiled.php';

if ( file_exists( $compiledPath ) )
{
    /** @noinspection PhpIncludeInspection */
    require $compiledPath;
}
