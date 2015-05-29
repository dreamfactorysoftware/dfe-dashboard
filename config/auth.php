<?php
//******************************************************************************
//* Authentication Configuration
//******************************************************************************

return [
    'driver'            => 'dashboard',
    'model'             => 'DreamFactory\\Enterprise\\Database\\Models\\User',
    'table'             => 'user_t',
    'open-registration' => true,
    'password'          => [
        'email'  => 'dfe-common::emails.password',
        'table'  => 'auth_reset_t',
        'expire' => 60,
    ],
];
