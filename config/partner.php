<?php
/**
 * @return array The partner configuration
 */
return [
    'do'      => [
        'name'          => 'Docomo',
        'class'         => DreamFactory\Enterprise\Dashboard\Partners\Docomo::class,
        'referrers'     => ['docomo.com', 'hubspot.com', 'dreamfactory.com'],
        'commands'      => [],
        'description'   => 'Docomo',
        'alert-context' => 'alert-partner well',
        'redirect-uri'  => null,
        'brand'         => [
            'action'    => <<< HTML
HTML

            ,
            'logo'      => '/img/partners/docomo-256x256.png',
            'icon'      => '/img/partners/docomo-256x100.png',
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy'      => <<< HTML
<div class="row partner-row">
    <div class="col-md-3"><img src="/img/partners/docomo-256x100.png" class="partner-brand"></div>
    <div class="col-md-6 partner-copy"><h3>Deploy DreamFactory&reg; to a dedicated server hosted by DOCOMO.</h3></div>
    <div class="col-md-3">
        <button type="button" class="btn btn-primary btn-lg btn-learn-more" data-toggle="modal" data-target="#learn-more">Learn More <i class="fa fa-fw fa-chevron-right"></i></button>
    </div>
</div>
HTML

            ,
        ],
    ],
    'default' => [
        'name'          => 'Default',
        'class'         => 'DreamFactory\\Enterprise\\Dashboard\\Partners\\DefaultPartner',
        'description'   => 'Default Partner Branding',
        'alert-context' => 'alert-danger',
        'redirect-uri'  => '',
        'brand'         => [
            'action'    => <<< HTML
HTML

            ,
            'logo'      => env('DFE_PARTNER_LOGO', null),
            'icon'      => env('DFE_PARTNER_ICON', null),
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy'      => <<< HTML
HTML

            ,
        ],
    ],
];
