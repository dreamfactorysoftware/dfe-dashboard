@extends('layouts.app')

@section('content')
	<div class="row">
		<div class="col-md-10 col-md-10-offset user-dashboard">
			<div id="alert-status-change" class="alert alert-info alert-block alert-fixed hide">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4>Notice</h4>

				<p>The status of one of your DSPs below has changed!</p>
			</div>

			{{ $_message }}

			<div class="dsp-list">
				{{ $_panelGroup }}
			</div>
		</div>

		<form id="_dsp-control" method="POST">
			<input type="hidden" name="id" value="">
			<input type="hidden" name="control" value="">
		</form>
	</div>
@endsection

