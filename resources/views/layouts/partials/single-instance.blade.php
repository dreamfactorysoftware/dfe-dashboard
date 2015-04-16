<div class="panel {{ $panelContext }} panel-instance">

	<div class="panel-heading" role="tab">
		<h4 class="panel-title" data-instance-name="{{ $instanceName or 'NEW' }}">
			@if( isset($headerIcon) )
				{{ $headerIcon }}
			@endif
			{{ $panelTitle }}
			@if ( isset($statusIcon) )
				<span class="pull-right"><i class="fa fa-fw {{ $statusIcon or null }} fa-1x"></i></span>
			@endif
		</h4>
	</div>

	<div class="panel-body instance-panel-body">

		<form id="{{ $formId }}" class="form-horizontal" method="POST">

			@if( isset($panelDescription) )
				<div class="panel-description">{!! $panelDescription !!}</div>
				<hr class="hr" />
			@endif

			@section('panel-body')
				@if( isset($panelBody) )
					{!! $panelBody !!}
				@endif
			@show

			@section('panel-captcha')
				@if ( config('dashboard.require-captcha') )
					<div class="form-group form-group-recaptcha">
						<label for="{{ $captchaId or 'rc-text-input' }}" class="col-md-2 control-label"></label>
						<div class="col-md-10">
							<div id="{{ $captchaId or 'rc-text-input' }}" class="g-recaptcha" data-sitekey="{{ config('recaptcha.siteKey') }}"></div>
							<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">{!! \Lang::get('dashboard.instance-proof-text') !!}</p>
						</div>
					</div>
				@endif
			@show

			<hr class="hr" />

			@section('panel-toolbar')
				@if ( isset($toolbarButtons) && !empty($toolbarButtons) )
					<div class="btn-toolbar" role="toolbar">
						<div class="btn-group" role="group">
							@foreach( $toolbarButtons as $_button )
								<button id="{{ $_button['id'] }}type="{{ $_button['type'] or 'button' }}" class="btn {{ $_button['class'] or 'btn-default' }}">{{ $_button['text'] }}</button>
							@endforeach
						</div>
					</div>
				@endif
			@show

			@section('panel-links')
				@if ( isset($instanceLinks) && !empty($instanceLinks))
					<div class="dsp-links">
						@foreach( $instanceLinks as $_linkId => $_link )
							<button id="{{ $_link['id'] or $_linkId }}" type="{{ $_link['type'] }}" class="btn {{ $_link['context'] or 'btn-success' }}">
								<i class="fa fa-fw {{ $_link['icon'] }}"></i>{{ $link['text'] }}</button>
						@endforeach
					</div>
				@endif
			@show

			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_provisioner" value="{{ DreamFactory\Library\Fabric\Database\Enums\GuestLocations::DFE_CLUSTER }}">
		</form>
	</div>
</div>
