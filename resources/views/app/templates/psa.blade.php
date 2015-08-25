<?php
//******************************************************************************
//* Generic alert for UI
//******************************************************************************

//  Wrap any id
!empty($alert_id) && $alert_id = ' id="' . $alert_id . '" ';
?>

<div {!! $alert_id !!} class="alert {{ $context }} alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times fa-fw"></i>
    </button>
    <h4>{!! $title !!}</h4>

    <p>{!! $content !!}</p>
</div>
