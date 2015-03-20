<?php
use DreamFactory\Oasys\Components\GenericUser;
use Kisma\Core\Utility\HtmlMarkup;

/**
 * @var WebController $this
 * @var array         $error
 * @var GenericUser   $profile
 * @var \stdClass     $objects
 */
if ( !isset( $profile ) || empty( $profile ) )
{
	$profile = array();
}
?>
	<h2 class="headline">Salesforce User Profile</h2>

	<p>Below is the user profile for the connected user:</p>
	<pre>
	<?php echo json_encode( $profile->toArray(), JSON_PRETTY_PRINT ); ?>
</pre>

	<p>These objects exist for this user:</p>
<?php
$_items = null;

if ( isset( $objects ) && !empty( $objects ) )
{
	foreach ( $objects->sobjects as $_object )
	{
		$_items .= '<li><a href="/web/force' . $_object->urls->describe . '" target="_blank">' . $_object->name . '</a></li>';
	}
}

echo <<<HTML
<ul class="force-item-list">
{$_items}
</ul>
HTML;
