@extends('layouts.dashboard-page')

@section('content')
    @include('app._page-header',array('pageName' => 'Roles'))

    <div class="row">
        <div class="col-md-12">
            <table class="table table-compact table-bordered table-striped table-hover table-heading table-datatable nowrap"
                   data-resource="role"
                   id="dt-role">
                <thead>
                    <tr>
                        <th data-column-name="id">ID</th>
                        <th data-column-name="role_name_text">Name</th>
                        <th data-column-name="active_ind">Active</th>
                        <th data-column-name="lmod_date">Last Modified</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop