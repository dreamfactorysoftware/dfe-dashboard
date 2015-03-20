<?php
//******************************************************************************
//* Authentication Configuration
//******************************************************************************

return [
    'driver'   => 'console',
    'model'    => 'DreamFactory\\Library\\Fabric\\Database\\Models\\Deploy\\ServiceUser',
    'table'    => 'service_user_t',
    'password' => [
        'email'  => 'emails.password',
        'table'  => 'auth_reset_t',
        'expire' => 60,
    ],

];
