<?php
//******************************************************************************
//* Routes
//******************************************************************************

use DreamFactory\Enterprise\Database\Models\Snapshot;

\Route::any('/', ['uses' => 'HomeController@index']);
\Route::any('home', ['uses' => 'HomeController@index']);
\Route::post('status/{id}', ['uses' => 'HomeController@status']);
\Route::post('control/{id?}', ['uses' => 'HomeController@control']);
\Route::post('upload', ['uses' => 'HomeController@upload']);
\Route::post('upload-package', ['uses' => 'HomeController@uploadPackage']);
\Route::controller('blueprint', 'BlueprintController');

\Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

/** Snapshot download handler */
\Route::get('/snapshot/{snapshotId}',
    function($snapshotId) {
        return Snapshot::downloadFromHash($snapshotId);
    });
