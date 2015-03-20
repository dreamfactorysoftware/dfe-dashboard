@extends('layouts.dashboard-page')

@section('content')
    @include('app._page-header',array('pageName' => 'Groups', 'buttons' => array('new'=>array('icon'=>'plus','color'=>'success')) ) )

    <div class="row">
        <div class="col-md-12">
            <table class="table table-compact table-bordered table-striped table-hover table-heading table-datatable nowrap"
                   data-resource="group"
                   id="dt-group">
                <thead>
                    <tr>
                        <th data-column-name="id">ID</th>
                        <th data-column-name="group_name_text">Name</th>
                        <th data-column-name="description_text">Description</th>
                        <th data-column-name="member_count_nbr">Members</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop