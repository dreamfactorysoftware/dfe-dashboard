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
		$_html .= view( 'layouts.partials._dashboard_item', $_item )->render();
	}
}

if ( isset( $groupHtml ) )
{
	$_html .= is_array( $groupHtml ) ? implode( PHP_EOL, $groupHtml ) : $groupHtml;
}

?>
<div class="panel-group" id="{{ $groupId }}" role="tablist" aria-multiselectable="true">
	{!! $_html !!}
</div>