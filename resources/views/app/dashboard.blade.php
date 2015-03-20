@extends('layouts.dashboard-page')

@section('page-title')
    Dashboard
@overwrite

@section('before-app-scripts')
    @parent
    <script src="/static/highcharts/4.0.4/highcharts.min.js"></script>
@stop

@section('after-app-scripts')
    @parent
    <script src="/js/chart-theme.js"></script>
    <script src="/js/cerberus.graphs.js"></script>
@stop


@section('content')
    <div class="row">
        <div class="col-md-12 dashboard-content">
            <div class="dashboard-heading">
                <h3 class="page-header">Overview</h3>

                <div class="hr"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">API Calls</div>
                </div>

                <div class="panel-body">
                    <div class="chart" id="timeline-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">API Calls (Back-end)</div>
                </div>

                <div class="panel-body">
                    <div class="chart" id="timeline-chart-fabric-api"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Logins</div>
                </div>

                <div class="panel-body">
                    <div class="chart" id="timeline-chart-logins"></div>
                </div>
            </div>
        </div>
    </div>
@stop
