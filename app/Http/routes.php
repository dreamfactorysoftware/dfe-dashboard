<?php
//******************************************************************************
//* Routes
//******************************************************************************

\Route::any( '/', ['uses' => 'HomeController@index'] );
\Route::any( 'home', ['uses' => 'HomeController@index'] );

\Route::controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);
