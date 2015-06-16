<?php
/**
 * @return array The partner configuration
 */
return [
    'vz' => [
        'name'          => 'Verizon',
        'class'         => 'DreamFactory\\Enterprise\\Partner\\AlertPartner',
        'referrers'     => ['verizon.com', 'hubspot.com', 'dreamfactory.com'],
        'commands'      => ['utc_post'],
        'description'   => 'Verizon Wireless',
        'alert-context' => 'alert-info',
        //'redirect-uri' => 'https://my.cloud.verizon.com/registration/#/',
        'brand'         => [
            'logo'      => '/vendor/dfe-partner/assets/img/logo-944x702.png',
            'icon'      => '/vendor/dfe-partner/assets/img/logo-135x100.png',
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy'      => <<< HTML
<h3>Why not move your instance to a shiny new Verizon Cloud? Click the button below to be swept away...</h3>
    <form method="POST" action="/partner/vz" class="pull-right">
   		<input type="hidden" name="_token" value="__CSRF_TOKEN__">
        <input type="hidden" name="command" value="utc_post">

        <button type="submit" class="btn btn-success">Read <strong>More</strong>...</button></p>
    </form>
    <div style="clear: both"></div>
HTML
            ,
        ],
    ],
];