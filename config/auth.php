<?php
//******************************************************************************
//* Authentication Configuration
//******************************************************************************

return [
    /** General */
    //  If true, users may self-register. Otherwise, admins must create users */
    'open-registration' => config('DFE_OPEN_REGISTRATION', true),
    /** Defaults */
    'defaults'          => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],
    /** Guards */
    'guards'            => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver'   => 'token',
            'provider' => 'users',
        ],
    ],
    /** Providers */
    'providers'         => [
        'users' => [
            'driver' => 'dashboard',
            'model'  => DreamFactory\Enterprise\Database\Models\User::class,
            'table'  => 'user_t',
        ],
    ],
    /** Password Resets */
    'passwords'         => [
        'users' => [
            'provider' => 'users',
            'email'    => 'dfe-common::emails.password',
            'table'    => 'auth_reset_t',
            'expire'   => 60,
        ],
    ],
];
