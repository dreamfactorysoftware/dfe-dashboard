@extends('layouts.partials.single-instance')

@section('panel-body')
	<div class="form-group">
		<label for="import-id" class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-import-label') }}</label>
		<div class="col-md-4">
			<select name="import-id"
					id="import-id"
					class="form-control" {{ 0 == count($snapshotList) ? 'disabled style="pointer-events: none;"' : null }}>
				@forelse( $snapshotList as $snapshot )
					<option value="{{ $snapshot['id'] }}">{{ $snapshot['name']}}</option>
				@empty
					<option value=>No snapshots found</option>
				@endforelse
			</select>
		</div>

		<div class="col-md-1">
			<p class="form-static-control control-label">or</p>
		</div>

		<div class="col-md-5">
			<label class="btn btn-primary btn-info btn-sm" for="import-file">
				<input id="import-file" type="file" style="display:none;">
				<i class="fa fa-fw fa-cloud-upload"></i> Upload Your Own!
			</label>
		</div>
	</div>
@overwrite

@section('panel-body-links')
	<button type="submit" class="btn btn-primary btn-warning btn-sm">
		<i class="fa fa-fw {{ config('dfe.icons.import') }}" style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-import-button-text') }}
	</button>
@overwrite
