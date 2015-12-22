<?php
use Carbon\Carbon;
use DreamFactory\Library\Utility\Flasher;

$_message = Flasher::getAlert();

$_psa = Flasher::psa('Alert',
        'Status updated as of ' . Carbon::now()->toFormattedDateString(),
        'alert-info alert-fixed',
        'dashboard-alert',
        true);
?>
@extends('layouts.app')

@section('content')
    {!! $_message !!}
    {!! $_psa !!}

    @if(isset($partnerContent))
        <div class="row">
            <div class="col-sm-12 col-md-12">
                {{--<div class="col-sm-offset-1 col-sm-10 col-sm-offset-1 col-md-offset-1 col-md-10 col-md-offset-1">--}}
                {!! $partnerContent !!}
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12 col-md-12">
            {{--<div class="col-sm-offset-1 col-sm-10 col-sm-offset-1 col-md-offset-1 col-md-10 col-md-offset-1">--}}
            <div class="create-instance">
                {!! $instanceCreator !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <hr class="hr" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="instance-panel-group">
                @if (is_string($instances))
                    {!! $instances !!}
                @elseif(is_array($instances))
                    @foreach( $instances as $_name => $_instance )
                        {!! $_instance->render() !!}
                    @endforeach
                @else
                    <h3 class="no-instances">You have no instances</h3>
                @endif
            </div>
        </div>
    </div>

    <!--suppress HtmlUnknownTarget -->
    <form id="_dsp-control" method="POST" action="/control">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="extra" value="">
        <input type="hidden" name="control" value="">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>
@endsection
