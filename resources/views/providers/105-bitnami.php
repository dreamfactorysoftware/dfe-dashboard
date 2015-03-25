<?php
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\Option;

/**
 * supplies info for the roll your own list
 */
if ( false !== ( $_options = Pii::getParam( 'dashboard.bitnami', false ) ) )
{
	$_featureEnabled = Option::get( $_options, 'enabled', false );
	$_url = Option::get( $_options, 'url' );
	$_wikiUrl = Option::get( $_options, 'wiki_url' );

	if ( $_featureEnabled )
	{
		$_body = <<<HTML
<p>Click the link to launch a DSP using a BitNami stack:<br/>
<a target="_blank" href="{$_url}">{$_url}</a></p>
<p>More information may be found in the README:
<br/>
<a target="_blank" href="{$_wikiUrl}">{$_wikiUrl}</a></p>
HTML;
	}
	else
	{
		$_body = <<<HTML
<p><strong>This feature is coming soon! Stay tuned!</strong></p>
HTML;
	}

	return array(
		'id'    => 'bitnami',
		'title' => 'BitNami',
		'body'  => $_body,
	);
}

return array();
