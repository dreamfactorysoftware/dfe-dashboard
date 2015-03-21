<?php
//******************************************************************************
//* Authentication Configuration
//******************************************************************************

return [
    'driver'   => 'dashboard',
    'model'    => 'DreamFactory\\Library\\Fabric\\Database\\Models\\Deploy\\User',
    'table'    => 'user_t',
    'password' => [
        'email'  => 'emails.password',
        'table'  => 'auth_reset_t',
        'expire' => 60,
    ],

];
