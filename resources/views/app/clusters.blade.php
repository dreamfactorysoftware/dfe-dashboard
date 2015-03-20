@extends('layouts.dashboard-page')

@section('breadcrumb-title')
    Clusters
@stop

@section('content')
    @include('app._page-header',array('pageName' => 'Clusters', 'buttons' => array('new'=>array('icon'=>'plus','color'=>'success')) ) )

    <div class="row">
        <div class="col-md-12">
            <table class="table table-compact table-bordered table-striped table-hover table-heading table-datatable nowrap"
                   data-resource="cluster"
                   id="dt-cluster">
                <thead>
                    <tr>
                        <th data-column-name="id">ID</th>
                        <th data-column-name="cluster_id_text">Name</th>
                        <th data-column-name="subdomain_text">Sub-Domain</th>
                        <th data-column-name="lmod_date">Last Modified</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop