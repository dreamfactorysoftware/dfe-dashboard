<?php
//******************************************************************************
//* Routes
//******************************************************************************

\Route::any( '/', ['uses' => 'HomeController@index'] );
\Route::any( 'home', ['uses' => 'HomeController@index'] );
\Route::any( 'status/{id}', ['uses' => 'HomeController@status'] );

\Route::controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);
