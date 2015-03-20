<?php
/**
 * SMTP mail settings
 */
return [
    'driver'     => env( 'SMTP_DRIVER', 'mailgun' ),
    'host'       => env( 'SMTP_HOST', 'smtp.mailgun.org' ),
    'port'       => env( 'SMTP_PORT', 587 ),
    'from'       => ['address' => env( 'MAIL_FROM_ADDRESS', 'no.reply@dreamfactory.com' ), 'name' => env( 'MAIL_FROM_NAME', 'DFE Console' )],
    'encryption' => 'tls',
    'username'   => env( 'MAIL_USERNAME' ),
    'password'   => env( 'MAIL_PASSWORD' ),
    'sendmail'   => '/usr/sbin/sendmail -bs',
    'pretend'    => false,
];
