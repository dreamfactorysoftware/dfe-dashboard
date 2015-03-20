<?php
//******************************************************************************
//* Authentication Configuration
//******************************************************************************

use DreamFactory\Enterprise\Dashboard\Providers\DashboardAuthProvider;

return [
    'driver'   => DashboardAuthProvider::IOC_NAME,
    'model'    => 'DreamFactory\\Library\\Fabric\\Database\\Models\\Deploy\\User',
    'table'    => 'user_t',
    'password' => [
        'email'  => 'emails.password',
        'table'  => 'auth_reset_t',
        'expire' => 60,
    ],

];
