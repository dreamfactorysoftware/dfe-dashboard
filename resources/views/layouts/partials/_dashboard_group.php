<?php
/**
 * Default template for a single chunk in the accordion
 *
 * @var WebController $this
 * @var string        $groupId
 * @var array         $groupItems
 * @var string        $groupHtml
 */

$_html = null;

if ( !empty( $groupItems ) )
{
	foreach ( $groupItems as $_item )
	{
		$_html .= $this->renderPartial( '_dashboard_item', $_item, true );
	}
}

if ( isset( $groupHtml ) )
{
	$_html .= is_array( $groupHtml ) ? implode( PHP_EOL, $groupHtml ) : $groupHtml;
}

?>
<div class="panel-group" id="<?php echo $groupId; ?>">
	<?php echo $_html; ?>
</div>