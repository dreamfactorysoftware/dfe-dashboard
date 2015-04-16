<?php
/**
 * @var bool $_requireCaptcha
 */

use DreamFactory\Library\Fabric\Database\Enums\GuestLocations;
use DreamFactory\Library\Utility\Inflector;

$_requireCaptcha = config( 'dashboard.require-captcha' );

if ( $_requireCaptcha )
{
	$_captcha = new Captcha();
	$_captcha->setPublicKey( config( 'recaptcha.public_key' ) );
	$_captcha->setPrivateKey( config( 'recaptcha.private_key' ) );

	$_html = $_captcha->displayHTML(
		config( 'recaptcha.theme', 'white' ),
		config( 'recaptcha.options', array() )
	);

	$_captchaHtml = <<<HTML
<div class="form-group">
    <label for="captcha" class="col-md-2 control-label">Validation Code</label>
    <div class="col-md-8">
        <div class="recaptcha">{$_html}</div>
        <p class="help-block"
            style="margin-top:2px;font-size: 13px;color:#888;">Please enter the validation code in the picture. This is a spam-prevention measure.</p>
    </div>
</div>
HTML;
}

$_dspName = ( \Auth::user()->admin_ind != 1 ? 'dsp-' : null ) . Inflector::neutralize( str_replace( ' ', '-', \Auth::user()->display_name_text ) );
$_token = csrf_token();
$_guest = GuestLocations::DFE_CLUSTER;
?>
<div class="panel {{ $panelContext }} panel-instance">
	<div class="panel-heading" role="tab">
		<h4 class="panel-title"><i class="fa fa-fw {{ config('dashboard.icons.create') }}"></i>{{ \Lang::get('dashboard.instance-create-title') }}</h4>
	</div>
	<div id="dsp_new"
		 class="panel-body-open"
		 role="tabpanel"
		 aria-labelledby="heading-dsp_list">
		<div class="panel-body">
			<div class="dsp-info">
				<form id="form-provision" class="form-horizontal" method="POST">
					<input type="hidden" name="_token" value="{{ $_token }}">
					<input type="hidden" name="_provisioner" value="{{ $_guest }}">
					<div class="panel-description">{!! \Lang::get('dashboard.instance-create') !!}</div>
					<div class="clearfix"></div>

					<div class="form-group">
						<label for="dsp_name" class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-name-label') }}</label>
						<div class="col-md-10">
							<div class="input-group">
								<input type="text" required name="id" id="dsp_name" class="form-control" placeholder="{{ $_dspName }}">
								<span class="input-group-addon">{{ $defaultDomain }}</span>
							</div>
						</div>
					</div>

					<div class="form-group form-group-recaptcha">
						<label for="rc-create" class="col-md-2 control-label"></label>
						<div class="col-md-10">
							<div id="rc-create" class="g-recaptcha" data-sitekey="{{ config('recaptcha.siteKey') }}"></div>
							<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">{!! \Lang::get('dashboard.instance-proof-text') !!}</p>
						</div>
					</div>

					<div class="dsp-links">
						<hr class="hr" />
						<button id="start-trial" type="submit" class="btn btn-primary btn-success"><i class="fa fa-fw fa-rocket"
																									  style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-create-button-text') }}
						</button>
					</div>

					<input type="hidden" name="control" value="create">
				</form>
			</div>

		</div>
	</div>
</div>