<?php
//******************************************************************************
//* Authentication Configuration
//******************************************************************************

return [
    'driver'            => 'dashboard',
    'model'             => DreamFactory\Enterprise\Database\Models\User::class,
    'table'             => 'user_t',
    //  If true, users may self-register. Otherwise, admins must create users */
    'open-registration' => config('DFE_OPEN_REGISTRATION', true),
    'password'          => [
        'email'  => 'dfe-common::emails.password',
        'table'  => 'auth_reset_t',
        'expire' => 60,
    ],
];
