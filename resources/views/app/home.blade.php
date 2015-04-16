<?php
$_message = null;

if ( \Session::has( 'dashboard-success' ) )
{
	$_flash = \Session::get( 'dashboard-success' );

	$_message = <<<HTML
                <div class="alert alert-success fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Success!</h4>

                    <p>{$_flash}</p>
                </div>
HTML;
}
elseif ( \Session::has( 'dashboard-failure' ) )
{
	$_flash = \Session::get( 'dashboard-failure' );

	$_message = <<<HTML
                <div class="alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Fail!</h4>

                    <p>{$_flash}</p>
                </div>
HTML;
}

?>
@extends('layouts.app')

@section('content')
	<div id="alert-status-change" class="alert alert-info alert-block alert-fixed hide">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<strong>Alert</strong> The status of one of your instances has changed!
	</div>

	{!! $_message !!}

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

	<form id="_dsp-control" method="POST">
		<input type="hidden" name="id" value="">
		<input type="hidden" name="control" value="">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
	</form>
@endsection

