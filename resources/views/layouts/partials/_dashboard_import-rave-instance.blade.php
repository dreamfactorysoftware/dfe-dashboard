<?php
/**
 * @var bool $_requireCaptcha
 */

use DreamFactory\Library\Fabric\Database\Enums\GuestLocations;
use DreamFactory\Library\Utility\Inflector;

$_captchaHtml = $_html = null;
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
    <label for="captcha" class="col-md-2 control-label">Organic Detection</label>
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
<div class="panel panel-{{ config('dashboard.import-panel-context','info') }} panel-dsp">
	<div class="panel-heading" role="tab">
		<h4 class="panel-title">
			<span class="instance-heading-dsp-name pull-left"><i class="fa fa-fw fa-cloud-upload"></i>Have an existing snapshot?</span>
		</h4>
	</div>
	<div id="import-instance" class="panel-body">
		<h3 class="dsp-box-heading">Restore an Instance</h3>

		<div class="dsp-info">
			<form id="form-import" class="form-horizontal" method="POST">
				<input type="hidden" name="_token" value="{{ $_token }}">
				<input type="hidden" name="_provisioner" value="{{ $_guest }}">

				{!! config('dashboard.import-instance-html') !!}

				<div class="clearfix"></div>

				<div class="form-group">
					<label for="import-id" class="col-md-2 control-label">Export</label>
					<div class="col-md-4">
						<select name="import-id"
								id="import-id"
								class="form-control" {{ 0 == count($snapshotList) ? 'disabled style="pointer-events: none;"' : null }}>
							@forelse( $snapshotList as $snapshot )
								<option value="{{ $snapshot->snapshot_id }}">{{ $snapshot->snapshot_name}}</option>
							@empty
								<option value=>No snapshots found</option>
							@endforelse
						</select>
					</div>

					<label for="import-file-label" class="col-md-1 control-label">or</label>
					<div class="col-md-5">
						<label class="btn btn-primary" for="import-file">
							<input id="import-file" type="file" style="display:none;">
							<i class="fa fa-fw fa-cloud-upload"></i> Upload Your Own!
						</label>
					</div>
				</div>

				{{ $_captchaHtml }}

				<div class="dsp-links">
					<button type="submit" class="btn btn-primary btn-success"><i class="fa fa-fw fa-{{ config('dashboard.import-panel-icon') }}"
																				 style="margin-right: 8px;"></i> Import
					</button>
				</div>

				<div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
			</form>
		</div>

	</div>
</div>
