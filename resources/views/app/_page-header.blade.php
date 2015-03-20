<?php
use DreamFactory\Library\Utility\IfSet;
use DreamFactory\Library\Utility\Inflector;

$_html = null;
$idPrefix = isset( $idPrefix ) ? $idPrefix : 'header-bar-';
$pageName = isset( $pageName ) ? $pageName : 'Page';
$_buttons = array(
        'new'    => array(
                'icon'    => 'plus',
                'color'   => 'success',
                'hidden'  => false,
                'tooltip' => 'Add a new record',
                'route'   => 'create',
        ),
        'edit'   => array(
                'icon'    => 'pencil',
                'color'   => 'warning',
                'hidden'  => true,
                'tooltip' => 'Modify selected record',
                'route'   => '{:id}/edit',
        ),
        'delete' => array(
                'icon'    => 'minus',
                'color'   => 'danger',
                'hidden'  => true,
                'tooltip' => 'Delete selected record',
                'route'   => '{:id}/destroy',
        ),
);

$_type = rtrim( Inflector::neutralize( $pageName ), 's' );

foreach ( $_buttons as $_id => $_options )
{
    $_route = $_options['route'];
    $_options = $_buttons[$_id];

    $_html .=
            '<button rel="/api/v1/' .
            $_type .
            's/' . $_route . '" data-toggle="tooltip" data-placement="left" title="' .
            IfSet::get( $_options, 'tooltip' ) .
            '" data-type="' .
            $_type .
            '" class="btn ' .
            IfSet::get( $_options, 'color', 'btn-info' ) .
            ' btn-sm btn-page-header" id="' .
            $idPrefix .
            $_id .
            '"' .
            ( $_options['hidden'] ? ' style="display: none;" ' : null ) .
            '>';

    if ( null !== ( $_icon = IfSet::get( $_options, 'icon' ) ) )
    {
        $_html .= '<i class="fa fa-fw fa-' . $_icon . '"></i>';
    }

    $_html .= '</button>';
}

unset( $_id, $_options );

if ( $_html )
{
    $_html = '<div class="page-header-toolbar pull-right">' . $_html . '</div>';
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="pull-left">
            <h3 class="page-header">{{ $pageName }}
                <div id="loading-data" style="display: none;"><i class="fa fa-fw fa-cog fa-spin text-warning"></i></div>
            </h3>
        </div>
        {!! $_html !!}
        <div class="hr"></div>
    </div>
</div>
