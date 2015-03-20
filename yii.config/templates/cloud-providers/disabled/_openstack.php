<?php
/**
 * supplies info for the roll your own list
 */

$_featureEnabled = Pii::getParam( 'dashboard.dreamstrap_fe_openstack' );

if ( $_featureEnabled )
{
	$_body = <<<HTML
<p><strong>This feature is coming soon! Stay tuned!</strong></p>
HTML;
}
else
{
	$_body = <<<HTML
<p><strong>This feature is coming soon! Stay tuned!</strong></p>
HTML;
}

return array(
	'id'    => 'openstack',
	'title' => 'OpenStack',
	'body'  => $_body,
);
