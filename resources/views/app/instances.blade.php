@extends('layouts.dashboard-page')

@section('content')
    @include('app._page-header',array('pageName' => 'Instances', 'buttons' => array('new'=>array('icon'=>'plus','color'=>'success')) ) )

    <div class="row">
        <div class="col-md-12">
            <table class="table table-compact table-bordered table-striped table-hover table-select table-datatable nowrap"
                   data-resource="instance"
                   id="dt-instance">
                <thead>
                    <tr>
                        <th data-column-name="id">ID</th>
                        <th data-column-name="instance_id_text">Name</th>
                        <th data-column-name="cluster_id_text">Cluster</th>
                        <th data-column-name="create_date">Created On</th>
                        <th data-column-name="email_addr_text">Owner Email</th>
                        <th data-column-name="lmod_date">Last Visit</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop