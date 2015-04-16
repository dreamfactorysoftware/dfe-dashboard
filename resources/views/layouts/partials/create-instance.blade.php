<?php


?>
<div class="panel {{ $panelContext }} panel-instance">
	<div class="panel-heading" role="tab">
		<h4 class="panel-title"><i class="fa fa-fw {{ config('dashboard.panels.create.header-icon') }}"></i>{{ \Lang::get('dashboard.instance-create-title') }}
		</h4>
	</div>
	<div id="dsp_new"
		 class="panel-body-open"
		 role="tabpanel">
		<div class="panel-body">
			<div class="dsp-info">
				<form id="form-provision" class="form-horizontal" method="POST">
					<div class="panel-description">{!! \Lang::get('dashboard.instance-create') !!}</div>
					<hr class="hr" />

					<div class="form-group">
						<label for="dsp_name" class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-name-label') }}</label>
						<div class="col-md-10">
							<div class="input-group">
								<input type="text" required name="id" id="dsp_name" class="form-control" placeholder="{{ $defaultInstanceName }}">
								<span class="input-group-addon">{{ $defaultDomain }}</span>
							</div>
						</div>
					</div>

					<div class="form-group form-group-recaptcha">
						<label for="rc-create" class="col-md-2 control-label"></label>
						<div class="col-md-10">
							<div id="rc-create" class="g-recaptcha" data-sitekey="{{ config('recaptcha.siteKey') }}"></div>
							<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">{!! \Lang::get('dashboard.instance-proof-text') !!}</p>
						</div>
					</div>

					<div class="dsp-links">
						<hr class="hr" />
						<button id="start-trial" type="submit" class="btn btn-primary btn-success"><i class="fa fa-fw fa-rocket"
																									  style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-create-button-text') }}
						</button>
					</div>

					<input type="hidden" name="control" value="create">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="_provisioner" value="{{ GuestLocations }}">
				</form>
			</div>

		</div>
	</div>
</div>