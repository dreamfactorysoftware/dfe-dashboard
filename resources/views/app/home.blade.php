@extends('layouts.app')
<?php

use DreamFactory\Enterprise\Partner\SitePartner;$_message = null;

if ( \Session::has( 'dashboard-success' ) ) {
	$_flash = \Session::get( 'dashboard-success' );

	$_message = <<<HTML
                <div class="alert alert-success fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Success!</h4>

                    <p>{$_flash}</p>
                </div>
HTML;
}
elseif ( \Session::has( 'dashboard-failure' ) ) {
	$_flash = \Session::get( 'dashboard-failure' );

	$_message = <<<HTML
                <div class="alert alert-danger fade in alert-dismissable alert-fixed">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Fail!</h4>

                    <p>{$_flash}</p>
                </div>
HTML;
}

$_partnerContent = ( isset($partner) && $partner instanceof SitePartner) ? $partner->getWebsiteContent() : null;
?>

@section('content')
	<div id="alert-status-change" class="alert alert-info alert-block alert-fixed hide">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<strong>Alert</strong> The status of one of your instances has changed!
	</div>

	{!! $_message !!}

	<div class="panel-group" id="panel-accordion" role="tablist" aria-multiselectable="true">
		@if($_partnerContent)
			<div class="row">
				<div class="col-md-12 col-sm-12">
					{!! $_partnerContent !!}
				</div>
			</div>
		@endif

		<div class="row">
			<div class="col-md-6">
				<div class="create-instance">
					{!! $instanceCreator !!}
				</div>
			</div>

			<div class="col-md-6">
				<div class="import-snapshot">
					{!! $snapshotImporter !!}
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<hr class="hr" />
		</div>
	</div>

	<div class="row">
		<div class="instance-panel-group">
			@if ( is_string($instances) )
				{!! $instances !!}
			@elseif( is_array($instances))
				@foreach( $instances as $_name => $_instance )
					{!! $_instance->render() !!}
				@endforeach
			@else
				<div class="col-md-12">
					<h3 style="text-align: center">No instances</h3>
				</div>
			@endif
		</div>
	</div>

	<form id="_dsp-control" method="POST" action="/control">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="control" value="">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
	</form>
@endsection

