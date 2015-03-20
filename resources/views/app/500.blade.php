@extends('layouts.dashboard-page')

@section('content')
	<div class="row">
		<div class="col-md-12 error-content">
			<div class="error-heading">
				<h3 class="page-header">We're terribly sorry, but...</h3>

				<div class="hr"></div>
			</div>

			<div class="panel panel-danger">
				<div class="panel-heading">
					<div class="panel-title">System Error</div>
				</div>

				<div class="panel-body">
					<p>Whatever you tried to access is not working properly. The powers that be have been notified.</p>

					<p><a href="/">Go Home</a></p>
				</div>
			</div>
		</div>
	</div>
@stop
