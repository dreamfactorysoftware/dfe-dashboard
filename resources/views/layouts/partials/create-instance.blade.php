<?php $noForm = true; ?>
@extends('layouts.partials.single-instance')

@section('panel-body')
    <ul class="nav nav-tabs" role="tablist" id="instance-create-tabs">
        <li role="presentation" class="active"><a href="#new-instance" aria-controls="new-instance" role="tab" data-toggle="tab">New Instance</a></li>
        @if( !empty( $importables ) )
            <li role="presentation"><a href="#import-instance" aria-controls="import-instance" role="tab" data-toggle="tab">Restore from Export</a></li>
        @endif
        @if(config('dashboard.allow-import-uploads'))
            <li role="presentation"><a href="#upload-instance" aria-controls="upload-instance" role="tab" data-toggle="tab">Upload an Export</a></li>
        @endif
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="new-instance">
            {{--@if( isset($panelDescription) && !empty($panelDescription) )--}}
            {{--<div class="panel-description">{!! $panelDescription !!}</div>--}}
            {{--<hr class="hr" />--}}
            {{--@endif--}}

            <form id="form-create" class="form-horizontal" method="POST">
                <div class="form-group">
                    <label for="instance-id"
                           class="col-md-2 control-label">{{ \Lang::get('common.instance-name-label') }}</label>

                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text"
                                   maxlength="64"
                                   required
                                   name="instance-id"
                                   id="instance-id"
                                   class="form-control"
                                   placeholder="{{ $defaultInstanceName }}">
                            <span class="input-group-addon">{{ $defaultDomain }}</span>
                        </div>
                        {!! \Lang::get('common.instance-create-help') !!}
                    </div>
                    <div class="col-md-2">
                        <button id="btn-create-instance" type="submit" class="btn btn-primary btn-success btn-md"
                                data-instance-action="create">
                            <i class="fa fa-fw {{ config('icons.create') }} fa-move-right"></i><span>{{ \Lang::get('common.instance-create-button-text') }}</span>
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
        </div>

        @if( !empty( $importables ) )
            <div role="tabpanel" class="tab-pane " id="import-instance">
                <form id="form-import" class="form-horizontal" method="POST">
                    <div class="form-group">
                        <label for="import-id" class="col-md-2 control-label">{{ \Lang::get('common.instance-import-label') }}</label>

                        <div class="col-md-8">
                            <select name="import-id"
                                    id="import-id"
                                    class="form-control">
                                <option data-instance-id="" value="">Select an export...</option>
                                @foreach( $importables as $importable )
                                    <option data-instance-id="{{ $importable['instance-id'] }}" value="{{ $importable['name'] }}">
                                        {{ $importable['instance-name']}}&nbsp;({{ $importable['export-date'] }})
                                    </option>
                                @endforeach
                            </select>
                            {!! \Lang::get('common.instance-import-help') !!}
                        </div>
                        <div class="col-md-2">
                            <button id="btn-import-instance" type="submit" class="btn btn-primary btn-success btn-md"
                                    data-instance-action="import">
                                <i class="fa fa-fw {{ config('icons.import') }} fa-move-right"></i><span>{{ \Lang::get('common.instance-import-button-text') }}</span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="instance-id" value="">
                    <input type="hidden" name="snapshot-id" value="">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_provisioner"
                           value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
                </form>
            </div>
        @endif

        @if(config('dashboard.allow-import-uploads'))

            <div role="tabpanel" class="tab-pane" id="upload-instance">
                <form id="form-upload" class="form-horizontal" method="POST" action="/upload" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="instance-id"
                               class="col-md-2 control-label">{{ \Lang::get('common.instance-id-label') }}</label>

                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text"
                                       maxlength="64"
                                       required
                                       name="instance-id"
                                       id="instance-id"
                                       class="form-control"
                                       placeholder="{{ $defaultInstanceName }}">
                                <span class="input-group-addon">{{ $defaultDomain }}</span>
                            </div>
                            {!! \Lang::get('common.instance-create-help') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="upload-file"
                               class="col-md-2 control-label"
                               style="padding-top:0; vertical-align:middle;">{{ \Lang::get('common.instance-upload-label') }}</label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" name="upload-file" id="upload-file" accept="application/gz,application/zip">
                            {!! \Lang::get('common.instance-upload-help') !!}
                        </div>
                        <div class="col-md-2">
                            <button id="btn-upload-instance" type="submit" class="btn btn-primary btn-success btn-md" data-instance-action="upload">
                                <i class="fa fa-fw {{ config('icons.upload') }} fa-move-right"></i><span>{{ \Lang::get('common.instance-upload-button-text') }}</span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_provisioner" value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
                </form>
            </div>
        @endif
    </div>

    <input type="hidden" name="control" value="create">
@overwrite
