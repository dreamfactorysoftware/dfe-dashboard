<?php
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\Option;

/**
 * supplies info for the roll your own list
 */

if ( false !== ( $_options = Pii::getParam( 'dashboard.rackspace', false ) ) )
{
	$_featureEnabled = Option::get( $_options, 'enabled', false );

	if ( $_featureEnabled )
	{
		$_body
			= <<<HTML
<p>Rackspace has no specific image. Instead, install one of our pre-built packages! For Redhat or CentOS flavors,
				see our <a href="http://wiki.dreamfactory.com/wiki/Packages/Rpm" target="_blank">installation guide</a>.  We also have one for Debian/Ubuntu systems <a
		href="http://wiki.dreamfactory.com/wiki/Packages/Deb" target="_blank">here</a>.</p>
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
		'id'    => 'rackspace',
		'title' => 'Rackspace',
		'body'  => $_body,
	);
}

return array();
