<?php
use Illuminate\Contracts\Http\Kernel;

//  Composer...
require __DIR__ . '/../bootstrap/autoload.php';

//  Us...
$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @type Kernel $kernel */
$kernel = $app->make( 'Illuminate\Contracts\Http\Kernel' );

$response = $kernel->handle( $request = Illuminate\Http\Request::capture() );
$response->send();

$kernel->terminate( $request, $response );
