<?php
/**
 * @return array The partner configuration
 */
return [
    'vz' => [
        'name' => 'Verizon',
        'class' => 'DreamFactory\\Enterprise\\Dashboard\\Partners\\Verizon',
        'referrers' => ['verizon.com', 'hubspot.com', 'dreamfactory.com'],
        'commands' => ['utc_post'],
        'description' => 'Verizon Wireless',
        'alert-context' => 'alert-danger',
        'redirect-uri' => 'https://my.cloud.verizon.com/registration/#/',
        'brand' => [
            'action' => <<< HTML
HTML
            ,
            'logo' => '/vendor/dfe-partner/assets/img/logo-944x702.png',
            'icon' => '/vendor/dfe-partner/assets/img/logo-135x100.png',
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy' => <<< HTML
<div class="row partner-row">
    <div class="col-md-2 col-sm-3" style="padding-left: 0;"><img src="/vendor/dfe-partner/assets/img/logo-135x100.png" class="partner-brand"></div>
    <div class="col-md-8 col-sm-6 partner-copy""><h3>Deploy DreamFactory to your own server hosted on the Verizon cloud.</h3></div>
    <div class="col-md-2 col-sm-3">
        <form method="POST" action="/partner/vz" class="pull-right">
            <input type="hidden" name="_token" value="__CSRF_TOKEN__">
            <input type="hidden" name="command" value="utc_post">
            <button type="submit" class="btn btn-danger partner-button">Sign Up Now!</button>
        </form>
        <div style="clear: both"></div>
    </div>
</div>
HTML
            ,
        ],
    ],
];
