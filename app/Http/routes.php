<?php
//******************************************************************************
//* Routes
//******************************************************************************

use DreamFactory\Enterprise\Database\Models\Snapshot;

\Route::any('/', ['uses' => 'HomeController@index']);
\Route::any('home', ['uses' => 'HomeController@index']);
\Route::post('status/{id}', ['uses' => 'HomeController@status']);
\Route::post('control/{id?}', ['uses' => 'HomeController@control']);

\Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

/** Snapshot download handler */
\Route::get('/snapshot/{snapshotId}',
    function ($snapshotId){
        return Snapshot::downloadFromHash($snapshotId);
    });

/** Login event listener */
\Event::listen('auth.login',
    function (){
        \Auth::user()->update(['last_login_date' => date('c')]);
    });

