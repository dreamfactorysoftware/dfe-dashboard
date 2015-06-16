<?php
/**
 * @return array The partner configuration
 */
return [
    'vz' => [
        'name'         => 'Verizon',
        'referrers'    => ['verizon.com', 'hubspot.com', 'dreamfactory.com'],
        'commands'     => ['utc_post'],
        'description'  => 'Verizon Wireless',
        //'redirect-uri' => 'https://my.cloud.verizon.com/registration/#/',
        'brand'        => [
            'logo'      => '/partner/vz/img/logo-944x702.png',
            'icon'      => '/partner/vz/img/logo-135x100.png',
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy'      => <<< HTML
<h3>Why not move your instance to a shiny new Verizon Cloud? Click the button below to be swept away...</h3>
    <form method="POST" action="/partner/vz" class="pull-right">
        <input type="hidden" name="command" value="utc_post">

        <button type="button" class="btn btn-success">Read <strong>More</strong>...</button></p>
    </form>
    <div style="clear: both"></div>
HTML
            ,
        ],
    ],
];