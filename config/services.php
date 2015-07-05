<?php

/**
 * DFE core services configuration
 */
return [
    //******************************************************************************
    //* Mailgun
    //******************************************************************************
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET_KEY'),
    ],
];
