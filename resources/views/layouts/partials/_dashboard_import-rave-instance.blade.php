<?php
/**
 * @var bool $_requireCaptcha
 */

use DreamFactory\Library\Fabric\Database\Enums\GuestLocations;
use DreamFactory\Library\Utility\Inflector;

$_dspName = ( \Auth::user()->admin_ind != 1 ? 'dsp-' : null ) . Inflector::neutralize( str_replace( ' ', '-', \Auth::user()->display_name_text ) );
$_token = csrf_token();
$_guest = GuestLocations::DFE_CLUSTER;
?>
<div class="panel panel-{{ config('dashboard.import-panel-context','info') }} panel-dsp">
	<div class="panel-heading" role="tab">
		<h4 class="panel-title">
			<span class="instance-heading-dsp-name pull-left"><i class="fa fa-fw fa-cloud-upload"></i>{{ \Lang::get('dashboard.instance-import-title') }}</span>
		</h4>
	</div>
	<div id="import-instance" class="panel-body">
		<div class="dsp-info">
			<form id="form-import" class="form-horizontal" method="POST">
				<input type="hidden" name="_token" value="{{ $_token }}">
				<input type="hidden" name="_provisioner" value="{{ $_guest }}">
				{!! \Lang::get('dashboard.instance-import') !!}
				<div class="clearfix"></div>

				<div class="form-group">
					<label for="import-id" class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-import-label') }}</label>
					<div class="col-md-4">
						<select name="import-id"
								id="import-id"
								class="form-control" {{ 0 == count($snapshotList) ? 'disabled style="pointer-events: none;"' : null }}>
							@forelse( $snapshotList as $snapshot )
								<option value="{{ $snapshot->snapshot_id }}">{{ $snapshot->snapshot_name}}</option>
							@empty
								<option value=>No snapshots found</option>
							@endforelse
						</select>
					</div>

					<label for="import-file-label" class="col-md-1 control-label">or</label>
					<div class="col-md-5">
						<label class="btn btn-primary" for="import-file">
							<input id="import-file" type="file" style="display:none;">
							<i class="fa fa-fw fa-cloud-upload"></i> Upload Your Own!
						</label>
					</div>
				</div>

				<div class="form-group form-group-recaptcha">
					<label for="dsp_name" class="col-md-2 control-label"></label>
					<div class="col-md-10">

						<div id="rc-import" class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>

						<p class="help-block" style="margin-top:2px;font-size: 13px;color:#888;">{!! \Lang::get('dashboard.instance-proof-text') !!}</p>
					</div>
				</div>

				<div class="dsp-links">
					<hr class="hr" />

					<button type="submit" class="btn btn-primary btn-success"><i class="fa fa-fw {{ config('dashboard.icons.import') }}"
																				 style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-import-button-text') }}
					</button>
				</div>
			</form>
		</div>

	</div>
</div>
