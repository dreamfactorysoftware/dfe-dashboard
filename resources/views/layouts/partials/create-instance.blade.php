@extends('layouts.partials.single-instance')

@section('panel-body')
	<div class="form-group">
		<label for="instance-name" class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-name-label') }}</label>
		<div class="col-md-10">
			<div class="input-group">
				<input type="text"
					   minlength="3"
					   maxlength="64"
					   required
					   name="instance-name"
					   id="instance-name"
					   class="form-control"
					   placeholder="{{ $defaultInstanceName }}">
				<span class="input-group-addon">{{ $defaultDomain }}</span>
			</div>
		</div>
	</div>
	<input type="hidden" name="control" value="create">
@overwrite

@section('panel-body-links')
	<button id="btn-create-instance" type="submit" class="btn btn-primary btn-success btn-sm" data-instance-action="create">
		<i class="fa fa-fw {{ config('dashboard.icons.create') }}" style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-create-button-text') }}
	</button>
@overwrite
