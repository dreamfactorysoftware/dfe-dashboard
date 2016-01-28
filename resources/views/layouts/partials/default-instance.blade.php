@extends('layouts.partials.single-instance')

@section('panel-body')
    <div class="row">
        <div class="instance-status-text dsp-info col-sm-12 col-md-12">
            <div class="dsp-name instance-status-name">
                <a href="{{ $instanceUrl }}"
                   target="{{ $instanceUrlTarget or '_blank' }}"
                   class="dsp-launch-link instance-link">{{ $instanceName }}{{ $defaultDomain }}</a>
            </div>
            <div class="dsp-stats instance-status-stats">{{ $instanceStatusText }}</div>
        </div>
    </div>
@stop

@section('panel-body-links')
@stop
