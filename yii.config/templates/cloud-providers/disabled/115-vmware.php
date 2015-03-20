<?php
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\Option;

/**
 * supplies info for the roll your own list
 */

if ( false !== ( $_options = Pii::getParam( 'dashboard.vmware', false ) ) )
{
	$_featureEnabled = Option::get( $_options, 'enabled', false );
	$_wikiUrl = Option::get( $_options, 'wiki_url' );
	$_launchUrl = Option::get( $_options, 'launch_url' );

	if ( $_featureEnabled )
	{
		$_body
			= <<<HTML
<p>Our VMWare appliance is available at <a target="_blank" href="{$_launchUrl}">{$_launchUrl}</a>.</p>
<p>More information is available here:<br/> <a target="_blank" href="{$_wikiUrl}">{$_wikiUrl}</a></p>
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
		'id'    => 'vmware',
		'title' => 'VMWare Appliance',
		'body'  => $_body
	);
}

return array();