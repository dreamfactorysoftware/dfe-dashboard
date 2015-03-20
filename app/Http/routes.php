<?php
//******************************************************************************
//* Routes
//******************************************************************************

\Route::group(
    ['middleware' => 'auth'],
    function ()
    {
        \Route::get( 'home', 'HomeController@index' );
    }
);

\Route::controllers(
    [
        'auth'     => 'Auth\AuthController',
        'password' => 'Auth\PasswordController',
    ]
);
