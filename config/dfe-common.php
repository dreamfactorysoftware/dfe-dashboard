<?php
/**
 * Configuration file for the dfe-common library
 */
use DreamFactory\Enterprise\Common\Enums\MailTemplates;

return [
    //******************************************************************************
    //* General
    //******************************************************************************
    'display-name'  => 'Platform Dashboard',
    'version'       => 'v1.0.x-alpha',
    //******************************************************************************
    //* Mail template service
    //******************************************************************************
    'mail-template' => [
        'web-url'               => 'http://cerberus.fabric.dreamfactory.com/',
        'public-url'            => 'http://cerberus.fabric.dreamfactory.com/',
        'support-email-address' => 'support@dreamfactory.com',
        'confirmation-url'      => 'http://cerberus.fabric.dreamfactory.com/app/confirmation/',
        'smtp-service'          => 'localhost',
        //.........................................................................
        //. Templates
        //.........................................................................
        'templates'             => [
            MailTemplates::WELCOME              => array(
                'subject'  => 'Welcome to DreamFactory Developer Central!',
                'template' => 'welcome-confirmation.html',
            ),
            MailTemplates::PASSWORD_RESET       => array(
                'subject'  => 'Recover your DreamFactory password',
                'template' => 'recover-password.html',
            ),
            MailTemplates::PASSWORD_CHANGED     => array(
                'subject'  => 'Your Password Has Been Changed',
                'template' => 'password-changed.html',
            ),
            MailTemplates::NOTIFICATION         => array(
                'subject'  => null,
                'template' => 'notification.html',
            ),
            MailTemplates::SYSTEM_NOTIFICATION  => array(
                'subject'  => null,
                'template' => 'system-notification.html',
            ),
            MailTemplates::PROVISION_COMPLETE   => array(
                'subject'  => 'Your DSP is ready!',
                'template' => 'provisioning-complete.html',
            ),
            MailTemplates::DEPROVISION_COMPLETE => array(
                'subject'  => 'Your DSP was removed!',
                'template' => 'deprovisioning-complete.html',
            ),
        ],
    ],

];