<?php
/**
 * supplies info for the roll your own list
 */
$_body
	= <<<HTML
<p>Setting up DSP is quick and easy if you install one of our pre-built packages! For Redhat or CentOS flavors, see our <a href="http://wiki.dreamfactory.com/wiki/Packages/Rpm" target="_blank">installation guide</a>.  We also have one for Debian/Ubuntu systems <a href="http://wiki.dreamfactory.com/wiki/Packages/Deb" target="_blank">here</a>.</p>
HTML;

return array(
	'id'    => 'linux',
	'title' => 'Your Own Linux Server',
	'body'  => $_body,
);
