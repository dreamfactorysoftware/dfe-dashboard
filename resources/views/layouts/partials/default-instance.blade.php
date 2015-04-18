@extends('layouts.partials.single-instance')

@section('panel-body')
    <div class="row">
        <div class="col-sm-2">
            <div class="instance-status-icon {{ $instanceStatusContext }} pull-left well">
                <i class="fa fa-fw {{ $instanceStatusIcon }} {{ $instanceStatusIconSize or 'fa-3x' }}"></i>
            </div>
        </div>

        <div class="instance-status-text dsp-info col-sm-10">
            <div class="dsp-name instance-status-name">
                <a href="{{ $instanceUrl }}"
                    target="{{ $instanceUrlTarget or '_blank' }}"
                    class="dsp-launch-link instance-link">{{ $instanceName }}</a>
            </div>
            <div class="dsp-stats instance-status-stats">{{ $instanceStatusText }}</div>
        </div>
    </div>
@overwrite

@section('panel-body-links')
@overwrite
