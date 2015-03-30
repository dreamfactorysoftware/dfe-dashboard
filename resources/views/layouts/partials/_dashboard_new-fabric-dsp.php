<?php
/**
 * @var bool $_requireCaptcha
 */
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
						<label for="captcha" class="col-md-2 control-label">Validation Code</label>
						<div class="col-md-8">
							<div class="recaptcha">{$_html}</div>
							<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">Please enter the validation code in the picture. This is a spam-prevention measure.</p>
						</div>
					</div>
HTML;
}

$_dspName = ( \Auth::user()->admin_ind != 1 ? 'dsp-' : null ) . Inflector::neutralize( str_replace( ' ', '-', \Auth::user()->display_name_text ) );
$_token = csrf_token();

$_item = array(
    'opened'         => true,
    'groupId'        => 'dsp_list',
    'targetId'       => 'dsp_new',
    'triggerContent' => '<span class="instance-heading-dsp-name pull-left"><i class="fa fa-fw fa-plus"></i>Create an Instance</span>',
    'targetContent'  => <<<HTML
			<h3 class="dsp-box-heading">Create a new instance of the DreamFactory Services Platform</h3>
			<div class="dsp-info">
				<form id="form-provision" class="form-horizontal" method="POST">
		            <input type="hidden" name="_token" value="{$_token}">
										<p>Please enter the name of the new instance below, or you may keep the name we have selected for you. Letters, numbers, and dashes are the only characters allowed.</p>
					<div class="clearfix"></div>

					<div class="form-group">
						<label for="dsp_name" class="col-md-2 control-label">Name Your DSP</label>
						<div class="col-md-8">
							<div class="input-group">
								<input type="text" name="id" id="dsp_name" value="{$_dspName}" class="form-control" placeholder="{$_dspName}">
								<span class="input-group-addon">.cloud.dreamfactory.com</span>
							</div>
							<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">This may take a minute. We will send you an email when your platform is ready.</p>
						</div>
					</div>
					{$_captchaHtml}
					<div class="dsp-links">
						<button id="start-trial" type="submit" class="btn btn-primary btn-warning"><i class="fa fa-fw fa-rocket" style="margin-right: 8px;"></i> Create</button>
					</div>

					<input type="hidden" name="control" value="create">
				</form>
			</div>
HTML
);

unset( $_requireCaptcha, $_captcha, $_html, $_captchaHtml, $_dspName );

return $_item;
