<?php
//******************************************************************************
//* Routes
//******************************************************************************

\Route::any('/', ['uses' => 'HomeController@index']);
\Route::any('home', ['uses' => 'HomeController@index']);
\Route::post('partner/{id}', ['uses' => 'HomeController@partner']);
\Route::post('status/{id}', ['uses' => 'HomeController@status']);
\Route::post('control/{id?}', ['uses' => 'HomeController@control']);

\Route::controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);
