<?php $noForm = true; ?>
@extends('layouts.partials.single-instance')

@section('panel-body')
    <ul class="nav nav-tabs" role="tablist" id="instance-create-tabs">
        <li role="presentation" class="active">
            <a href="#new-instance"
              aria-controls="new-instance"
              role="tab"
              data-toggle="tab"
              data-tab-id="0">{!! trans('common.tab-names.new') !!}</a></li>
        <li role="presentation"><a href="#import-instance" aria-controls="import-instance" role="tab" data-toggle="tab" data-tab-id="1">{!!
                trans('common.tab-names.restore') !!}</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Create instance tab -->
        <div role="tabpanel" class="tab-pane fade in active" id="new-instance" data-tab-id="0">
            <form id="form-create" class="form-horizontal" method="POST" action="/upload-package" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="instance-id" class="col-md-2 control-label">{{ trans('common.instance-name-label') }}</label>
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
                        &nbsp;
                    </div>

                </div>
                @if( !empty( $offerings ) )
                    {!! $offerings !!}
                @endif

                @if(config('dashboard.allow-package-uploads'))
                    <div class="form-group">
                        <label for="upload-package" class="col-md-2 control-label">{!! trans('common.instance-package-label') !!}</label>

                        <div class="col-md-8">
                            <input type="file"
                                   class="form-control"
                                   name="upload-package"
                                   id="upload-package">
                            {!! trans('common.instance-package-help') !!}
                        </div>

                    </div>
                @endif
                <div class="form-group">
                    <label for="upload-file"
                           class="col-md-2 control-label">
                    </label>
                    <div class="col-md-8 text-right">
                        <button id="btn-create-instance" type="submit" class="btn btn-primary btn-success btn-md" data-instance-action="create">
                            <i class="fa fa-fw {{ config('icons.create') }} fa-move-right"></i><span>{{ trans('common.instance-create-button-text') }}</span>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_provisioner" value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
            </form>
        </div>


        <!-- Import instance tab -->
        <div role="tabpanel" class="tab-pane fade" id="import-instance" data-tab-id="1">
            <form id="form-import" class="form-horizontal" method="POST" action="/upload" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="import-id" class="col-md-2 control-label">{{ trans('common.instance-import-label') }}</label>
                    <div class="col-md-8">
                        <select name="import-id"
                                id="import-id"
                                class="form-control {{ empty($importables) ? 'disabled' : null }}"
                                data-import-count="{{ sizeof($importables) }}">
                            @if( !empty( $importables ) )
                                <option data-instance-id="" value="">{!! trans('common.instance-import-select-label') !!}
                                </option>
                                @foreach( $importables as $importable )
                                    <option data-instance-id="{{ $importable['instance-id'] }}" value="{{ $importable['name'] }}">
                                        {{ $importable['instance-name']}}&nbsp;({{ $importable['export-date'] }})
                                    </option>
                                @endforeach
                            @else
                                <option class="disabled" disabled="disabled">
                                    {{ trans('common.instance-import-empty-label') }}
                                </option>
                            @endif
                        </select>
                        &nbsp;
                    </div>


                    {{--@if(config('dashboard.allow-import-uploads'))--}}
                    {{--@if(config('dashboard.allow-package-uploads'))--}}
                </div>

                <div class="form-group">
                    <div class="col-md-offset-1 col-md-10 col-md-offset-1">
                        <div align="center"><strong>-or-</strong></div>
                    </div>
                </div>
                @if(config('dashboard.allow-import-uploads'))
                    <div class="form-group">
                        <label for="upload-file"
                               class="col-md-2 control-label">{{ trans('common.instance-upload-label') }}
                        </label>

                        <div class="col-md-8">
                            <input type="file" class="form-control" name="upload-file" id="upload-file" accept="application/gz,application/zip,application/tgz">
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="instance-id" class="col-md-2 control-label">{{ trans('common.instance-id-label') }}</label>
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
                    </div>


                </div>
                <div class="form-group">
                    <label for="upload-file"
                           class="col-md-2 control-label">
                    </label>
                        <div class="col-md-8 text-right">
                            <button id="btn-import-instance" type="submit" class="btn btn-primary btn-success btn-md" data-instance-action="import">
                                <i class="fa fa-fw {{ config('icons.create') }} fa-move-right"></i><span>{{ trans('common.instance-import-button-text') }}</span>
                            </button>
                        </div>
                </div>


                <input type="hidden" name="snapshot-id" value="">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_provisioner" value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
            </form>
        </div>
    </div>
@overwrite
