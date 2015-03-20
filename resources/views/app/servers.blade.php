@extends('layouts.dashboard-page')

@section('content')
    @include('app._page-header',array('pageName' => 'Servers', 'buttons' => array('new'=>array('icon'=>'plus','color'=>'success')) ) )

    <div class="row">
        <div class="col-md-12">
            <table class="table table-compact table-bordered table-striped table-hover table-heading table-datatable nowrap"
                   data-resource="server"
                   id="dt-server">
                <thead>
                    <tr>
                        <th data-column-name="id">ID</th>
                        <th data-column-name="server_id_text">Name</th>
                        <th data-column-name="type_name_text">Type</th>
                        <th data-column-name="host_text">Host</th>
                        <th data-column-name="lmod_date">Last Modified</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop