<?php
/**
 * @return array The partner configuration
 */
return [
    'default' => [
        'name'          => 'Partner',
        'class'         => DreamFactory\Enterprise\Dashboard\Partners\DefaultPartner::class,
        'description'   => 'Default Partner Branding',
        'alert-context' => 'alert-danger',
        'redirect-uri'  => '',
        'brand'         => [
            'action'    => null,
            'logo'      => env('DFE_PARTNER_LOGO'),
            'icon'      => env('DFE_PARTNER_ICON'),
            'copyright' => '&copy; ' . date('Y'),
            'copy'      => null,
        ],
    ],
];
