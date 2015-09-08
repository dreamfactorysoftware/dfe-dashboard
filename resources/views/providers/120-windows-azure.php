<?php
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\Option;

/**
 * supplies info for the roll your own list
 */

if ( false !== ( $_options = Pii::getParam( 'dashboard.azure', false ) ) )
{
	$_featureEnabled = Option::get( $_options, 'enabled', false );
	$_vhdId = Option::get( $_options, 'vhd_id' );
	$_vhdVersion = Option::get( $_options, 'vhd_version' );
	$_wikiUrl = Option::get( $_options, 'wiki_url' );
	$_launchUrl = str_replace( array( '{{vhd_id}}', '{{vhd_version}}' ), array( $_vhdId, $_vhdVersion ), Option::get( $_options, 'launch_url' ) );

	if ( $_featureEnabled )
	{
		$_body
			= <<<HTML
<p>Our Windows Azure DSP image is available in <a target="_blank" href="{$_launchUrl}">{$_launchUrl}</a>.</p>
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
		'id'    => 'windows-azure',
		'title' => 'Windows Azure',
		'body'  => $_body
	);
}

return array();
