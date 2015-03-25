<?php
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\Option;

/**
 * supplies info for the roll your own list
 */
if ( false !== ( $_options = Pii::getParam( 'dashboard.amazon_ec2', false ) ) )
{
	$_featureEnabled = Option::get( $_options, 'enabled', false );
	$_ami = Option::get( $_options, 'ami' );
	$_wikiUrl = Option::get( $_options, 'wiki_url' );
	$_launchUrl = str_replace( '{{ami}}', $_ami, Option::get( $_options, 'launch_url' ) );

	if ( $_featureEnabled )
	{
		$_body
			= <<<HTML
<p>Click the link to launch a DSP using our AMI:<br/><a target="_blank" href="{$_launchUrl}">{$_launchUrl}</a></p>
<p>A complete step-by-step tutorial is available in the wiki:<br/> <a target="_blank" href="{$_wikiUrl}">{$_wikiUrl}</a></p>
HTML;
	}
	else
	{
		$_body
			= <<<HTML
<p><strong>This feature is coming soon! Stay tuned!</strong></p>
HTML;
	}

	return array(
		'id'    => 'amazon-ec2',
		'title' => 'Amazon EC2',
		'body'  => $_body,
	);
}

return array();
