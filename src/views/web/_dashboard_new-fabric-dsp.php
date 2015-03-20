<?php
/**
 * @file
 * This is the template for the new hosted dsp block on the dashboard page
 *
 * @var array $_user
 */

use DreamFactory\Yii\Utility\Pii;
use ReCaptcha\Captcha;

$_html = null;

if ( !isset( $_user ) )
{
    $this->redirect( '/' );
}

//$_captcha = new Captcha();
//$_captcha->setPublicKey( Pii::getParam( 'recaptcha.public_key' ) );
//$_captcha->setPrivateKey( Pii::getParam( 'recaptcha.private_key' ) );

//$_html = $_captcha->displayHTML(
//    Pii::getParam( 'recaptcha.theme', 'white' ),
//    Pii::getParam( 'recaptcha.options', array() )
//);

$_dspName = ( $_user['admin_ind'] != 1 ? 'dsp-' : null ) . $_user['display_name_text'];

$_item = array(
    'opened'         => empty( $_dspList ),
    'groupId'        => 'dsp_list',
    'targetId'       => 'dsp_new',
    'triggerContent' => '<span class="instance-heading-dsp-name"><i class="fa fa-plus"></i>Get the Free Hosted Edition</span>',
    'targetContent'  => <<<HTML
			<div class="dsp-icon well pull-left"><i class="fa fa-check-square-o fa-3x" style="color: darkgreen;"></i></div>
			<div class="dsp-info">
				<form id="form-provision" class="form-horizontal" method="POST">
					<h3>Get the Free Hosted Edition of the DreamFactory Services Platform&trade;</h3>
					<p>We can host your DSP for you. Simply enter a name below, or you may keep the name we have selected for you. Letters, numbers, and dashes are the only characters allowed. If you would rather install on your own server click Developer Resources above for more information.</p>
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
<!--
					<div class="form-group">
						<label for="captcha" class="col-md-2 control-label">Validation Code</label>
						<div class="col-md-8">
							<div class="recaptcha">{$_html}</div>
							<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">Please enter the validation code in the picture. This is a spam-prevention measure.</p>
						</div>
					</div>
-->
					<div class="dsp-links">
						<button id="start-trial" type="submit" class="btn btn-primary btn-warning"><i class="fa fa-rocket" style="margin-right: 8px;"></i> Get Free Hosted Edition!</button>
					</div>

					<input type="hidden" name="control" value="create">
				</form>
			</div>
HTML
);

unset( $_captcha, $_html );

return $_item;
