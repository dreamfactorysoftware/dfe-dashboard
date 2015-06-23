<?php
/**
 * @return array The partner configuration
 */
return [
    'vz' => [
        'name'          => 'Verizon',
        'class'         => 'DreamFactory\\Enterprise\\Dashboard\\Partners\\Verizon',
        'referrers'     => ['verizon.com', 'hubspot.com', 'dreamfactory.com'],
        'commands'      => ['utc_post'],
        'description'   => 'Verizon Wireless',
        'alert-context' => 'alert-partner',
        'redirect-uri'  => 'https://my.cloud.verizon.com/registration/#/',
        'brand'         => [
            'action'    => null,
            'logo'      => '/vendor/dfe-partner/assets/img/logo-944x702.png',
            'icon'      => '/vendor/dfe-partner/assets/img/verizon_left.png',
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy'      => <<< HTML
<div class="row partner-row">
    <div class="col-md-2 col-sm-3 col-lg-2 partner-brand"><img src="/vendor/dfe-partner/assets/img/verizon_left.png" alt></div>
    <div class="col-md-8 col-sm-6 col-lg-8 partner-copy"><h3>Deploy DreamFactory to your own server hosted on the Verizon Cloud.</h3></div>
    <div class="col-md-2 col-sm-3 col-lg-2 partner-action">
        <button type="button" class="btn btn-danger btn-md partner-button" data-toggle="modal" data-target="#vzLearnMore">Learn More <i class="fa fa-fw fa-angle-double-right"></i></button>
    </div>
</div>

<div class="modal fade" id="vzLearnMore" tabindex="-1" role="dialog" aria-labelledby="DreamFactory on Verizon Cloud">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-fw fa-times"></i></span></button>
      <div class="modal-header">
        <span class="partner-brand"><img src="/vendor/dfe-partner/assets/img/verizon_left.png" alt><h3>DreamFactory on Verizon Cloud</h3></span>
      </div>
      <div class="modal-body">
        <p>Signing up for <strong>Verizon Cloud</strong> services allows you to host a <strong>DreamFactory</strong> instance on your own server outside of this free, sandbox environment.</p>

        <p id="signup">When you click 'Sign up now!' below, you'll be redirected to the <strong>Verizon Cloud Registration</strong> page where you can select the cloud services for your new server.</p>

        <p>After you purchase and create a server, you can install a new <strong>DreamFactory</strong> instance on the server using the appropriate <a href="//bitnami.com/stack/dreamfactory/installer" title="Bitnami DreamFactory Installer">Bitnami installer</a> for DreamFactory.</p>

        <p>More information is available on the <a href="//wiki.bitnami.com/Native_Installers_Quick_Start_Guide" title="Native Installers Quick Start Guide">Bitnami Native Installers Quick Start Guide</a></p>

        <p>After running the installer, you may access your new instance by going to the URL/port of your new instance in any browser.</p>

        <p>If you have questions or problems please contact <a href="mailto:support@dreamfactory.com">DreamFactory Support</a>.</p>
      </div>
      <div class="modal-footer">
        <form method="POST" action="/partner/vz" class="pull-right">
            <input type="hidden" name="_token" value="__CSRF_TOKEN__">
            <input type="hidden" name="command" value="utc_post">
            <button type="button" class="btn btn-info btn-md partner-button" data-dismiss="modal">Close</button>&nbsp;
            <button type="submit" class="btn btn-danger btn-md partner-button" style="background-color: #ED1C24">Sign Up Now!</button>
        </form>
      </div>
    </div>
  </div>
</div>
HTML
            ,
        ],
    ],
];
