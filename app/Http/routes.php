<?php
//******************************************************************************
//* Routes
//******************************************************************************

\Route::get( '/', ['as' => 'home', 'uses' => 'HomeController@index'] );

\Route::controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);
