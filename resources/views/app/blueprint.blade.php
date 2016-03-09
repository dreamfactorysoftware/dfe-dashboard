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
                {!! $partnerContent !!}
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="create-instance-blueprint">
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
