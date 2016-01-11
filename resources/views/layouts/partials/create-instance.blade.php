<?php $noForm = true; ?>
@extends('layouts.partials.single-instance')

@section('panel-body')
    <form id="form-create" class="form-horizontal" method="POST">
        <div class="form-group">
            <label for="instance-name"
                   class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-name-label') }}</label>

            <div class="col-md-8">
                <div class="input-group">
                    <input type="text"
                           maxlength="64"
                           required
                           name="instance-name"
                           id="instance-name"
                           class="form-control"
                           placeholder="{{ $defaultInstanceName }}">
                    <span class="input-group-addon">{{ $defaultDomain }}</span>
                </div>
                {!! \Lang::get('dashboard.instance-create-help') !!}
            </div>
            <div class="col-md-2">
                <button id="btn-create-instance" type="submit" class="btn btn-primary btn-success btn-md"
                        data-instance-action="create">
                    <i class="fa fa-fw {{ config('icons.create') }} fa-move-right"></i><span>{{ \Lang::get('dashboard.instance-create-button-text') }}</span>
                </button>
            </div>
        </div>
        @if( !empty( $offerings ) )
            {!! $offerings !!}
        @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_provisioner"
               value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
    </form>

    @if( !empty( $importables ) )
        <form id="form-import" class="form-horizontal" method="POST">
            <div class="row">
                <div class="col-md-offset-2 col-md-1"><h3 class="either-or">Or</h3></div>
            </div>

            <div class="form-group">
                <label for="import-id"
                       class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-import-label') }}</label>

                <div class="col-md-8">
                    <select name="import-id"
                            id="import-id"
                            class="form-control">
                        <option data-instance-id="" value="">Select an export...</option>
                        @foreach( $importables as $importable )
                            <option data-instance-id="{{ $importable['instance-id'] }}"
                                    value="{{ $importable['name'] }}">{{ $importable['instance-name']}}
                                ({{ $importable['export-date'] }})
                            </option>
                        @endforeach
                    </select>
                    {!! \Lang::get('dashboard.instance-import-help') !!}
                </div>
                <div class="col-md-2">
                    <button id="btn-import-instance" type="submit" class="btn btn-primary btn-success btn-md"
                            data-instance-action="import">
                        <i class="fa fa-fw {{ config('icons.import') }} fa-move-right"></i><span>{{ \Lang::get('dashboard.instance-import-button-text') }}</span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="instance-id" value="">
            <input type="hidden" name="snapshot-id" value="">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_provisioner"
                   value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
        </form>
    @endif

    @if(config('dfe.)

    <input type="hidden" name="control" value="create">
@overwrite
