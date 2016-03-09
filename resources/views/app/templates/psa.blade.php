<?php
/**
 * A generic UI alert
 *
 * @type string $context  The alert context (alert-info, alert-danger, etc.)
 * @type string $alert_id The optional HTML "id" of the alert div
 * @type string $title    (may contain HTML)
 * @type string $content  (may contain HTML)
 */
//  Wrap any id
!empty($alert_id) && $alert_id = ' id="' . $alert_id . '" ';
?>
<div class="alert {{ $context }} alert-dismissable" {{ $alert_id }}>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times fa-fw"></i>
    </button>
    <h4>{!! $title !!}</h4>
    <p>{!! $content !!}</p>
</div>
