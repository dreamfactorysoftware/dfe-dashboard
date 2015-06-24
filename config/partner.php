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
        'alert-context' => 'alert-danger',
        'redirect-uri'  => 'https://my.cloud.verizon.com/registration/#/',
        'brand'         => [
            'action'    => <<< HTML
HTML
            ,
            'logo'      => '/vendor/dfe-partner/assets/img/logo-944x702.png',
            'icon'      => '/vendor/dfe-partner/assets/img/verizon_left.png',
            'copyright' => '&copy; ' . date('Y') . ' Verizon',
            'copy'      => <<< HTML
<div class="row partner-row">
    <div class="col-md-2 col-sm-2" style="padding-left: 0;"><img src="/vendor/dfe-partner/assets/img/verizon_left.png" class="partner-brand"></div>
    <div class="col-md-8 col-sm-8 partner-copy"><h3>Deploy DreamFactory to your own virtual server hosted on a Verizon Cloud Space.</h3></div>
    <div class="col-md-2 col-sm-2">
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#vzLearnMore" style="background-color: #ED1C24; margin-top: 10%; margin-right: 0;">Learn More &gt;&gt;</button>
        <div style="clear: both"></div>
    </div>
</div>
<div class="modal fade" id="vzLearnMore" tabindex="-1" role="dialog" aria-labelledby="DreamFactory on Verizon Cloud">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <div class="modal-header">
        <div class="partner-copy" style="background-color: #ECEDEE; color: #333333; border-color: #ECEDEE"><img src="/vendor/dfe-partner/assets/img/verizon_left.png" class="partner-brand">DreamFactory on Verizon Cloud</div>
      </div>
      <div class="modal-body">
        <p>Signing up for Verizon Cloud services allows you to deploy a DreamFactory instance to your own virtual server on a Verizon Cloud Space, outside of the free sandbox environment you are currently in.</p>

        <p id="signup">When you click the 'Sign up now!' button you'll be redirected to the Verizon Cloud Registration page where you can purchase the cloud services for your new server.</p>

        <p>After you purchase and create a server, you can install a new DreamFactory instance on the server using the appropriate Linux or Windows 
        <a href="https://bitnami.com/stack/dreamfactory/installer" title="Bitnami DreamFactory Installer">Bitnami DreamFactory installer</a>.</p>

        <p>More information is available on the
        <a href="https://wiki.bitnami.com/Native_Installers_Quick_Start_Guide" title="https://wiki.bitnami.com/Native_Installers_Quick_Start_Guide">Bitnami Native Installers Quick Start Guide</a></p>

        <p>After running the installer you can go to the URL/port for your new DreamFactory instance in the browser to access the admin console for managing your instance.</p>

        <p>If you have questions or problems please contact <a href="mailto:support@dreamfactory.com">DreamFactory Support</a>.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>&nbsp;
        <form method="POST" action="/partner/vz" class="pull-right">
        <input type="hidden" name="_token" value="__CSRF_TOKEN__">
        <input type="hidden" name="command" value="utc_post">
            <button type="submit" class="btn btn-primary partner-button" style="background-color: #ED1C24">Sign Up Now!</button>
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
