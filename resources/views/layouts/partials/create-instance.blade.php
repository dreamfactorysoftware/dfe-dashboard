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
    @if( !empty( $offerings ) )
        {!! $offerings !!}
    @endif

    @if( !empty( $importables ) )
        <div class="row">
            <div class="col-md-offset-2 col-md-1"><h3 class="either-or">Or</h3></div>
        </div>

        <div class="form-group">
            <label for="import-id"
                   class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-import-label') }}</label>

            <div class="col-md-10">
                <select name="import-id"
                        id="import-id"
                        class="form-control">
                    @foreach( $importables as $importable )
                        <option data-instance-id="{{ $importable['instance-id'] }}" value="{{ $importable['name'] }}">{{ $importable['instance-name']}} ({{ $importable['export-date'] }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    <input type="hidden" name="control" value="create">
@overwrite

@section('panel-body-links')
    <button id="btn-create-instance" type="submit" class="btn btn-primary btn-success btn-sm" data-instance-action="create">
        <i class="fa fa-fw {{ config('icons.create') }}" style="margin-right: 8px;"></i> {{ \Lang::get('dashboard.instance-create-button-text') }}
    </button>
@overwrite
