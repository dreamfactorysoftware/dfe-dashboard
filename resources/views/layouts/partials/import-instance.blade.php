@extends('layouts.partials.single-instance')

@section('panel-body')
	<div class="form-group">
		<label for="import-id" class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-import-label') }}</label>
		<div class="col-md-10">
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
	</div>
@overwrite

@section('panel-body-links')
	<button type="submit" class="btn btn-primary btn-warning btn-sm">
		<i class="fa fa-fw {{ config('dfe.icons.import') }}" style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-import-button-text') }}
	</button>
@overwrite
