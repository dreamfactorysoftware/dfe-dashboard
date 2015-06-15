<?php
/**
 * @return array The partner configuration
 */
return [
    'vz' => [
        'name'        => 'Verizon',
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam viverra neque non porttitor consequat. Quisque et felis congue nunc euismod pulvinar. Phasellus vel lacinia magna, nec iaculis justo. Aenean pulvinar eu neque ac placerat. Sed pretium, odio a porta gravida, dolor ex vestibulum mauris, ut elementum tellus sapien nec ante. Praesent cursus varius magna, eleifend condimentum enim convallis sit amet. Ut pretium viverra faucibus. Aenean tincidunt interdum nulla ut tincidunt. Suspendisse sed erat a nibh consectetur iaculis. Donec ante velit, posuere ut diam vel, pulvinar luctus nibh.',
        'brand'       => [
            'logo'              => '/partner/vz/img/logo-944x702.png',
            'icon'              => '/partner/vz/img/logo-135x100.png',
            'copyright'         => '&copy; ' . date('Y') . ' Verizon',
            'copyright-minimal' => '&copy; ' . date('Y') . ' Verizon',
            'copy'              => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam viverra neque non porttitor consequat. Quisque et felis congue nunc euismod pulvinar. Phasellus vel lacinia magna, nec iaculis justo. Aenean pulvinar eu neque ac placerat. Sed pretium, odio a porta gravida, dolor ex vestibulum mauris, ut elementum tellus sapien nec ante. Praesent cursus varius magna, eleifend condimentum enim convallis sit amet. Ut pretium viverra faucibus. Aenean tincidunt interdum nulla ut tincidunt. Suspendisse sed erat a nibh consectetur iaculis. Donec ante velit, posuere ut diam vel, pulvinar luctus nibh.</p><p class="pull-right"><button type="button" class="btn btn-success">Buy Now!</button></p><div style="clear: both"></div>',
            'copy-minimal'      => <<< HTML
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam viverra neque non porttitor consequat. Quisque et felis congue nunc euismod pulvinar. Phasellus vel lacinia magna, nec iaculis justo. Aenean pulvinar eu neque ac placerat. Sed pretium, odio a porta gravida, dolor ex vestibulum mauris, ut elementum tellus sapien nec ante. Praesent cursus varius magna, eleifend condimentum enim convallis sit amet. Ut pretium viverra faucibus. Aenean tincidunt interdum nulla ut tincidunt. Suspendisse sed erat a nibh consectetur iaculis. Donec ante velit, posuere ut diam vel, pulvinar luctus nibh.</p><p class="pull-right">
<form method="POST" action="/api/v1/ops/partner">
<input type="hidden" name="command" value="utc_post">
<button type="button" class="btn btn-success">Buy Now!</button></p><div style="clear: both"></div>
</form>
HTML
            ,
        ],
    ],
];