@extends('layouts.main')

@section('content')
    <form class="form-horizontal" action="/api/v1/instances" method="POST">
        <div class="page-heading">
            <h3 class="page-header pull-left">{{ $pageHeader }}</h3>
                <span class="pull-right">
                <a class="btn btn-sm btn-page-header" data-toggle="tooltip" data-placement="bottom" title="Discard any changes"
                   href="/settings/instances"><i class="fa fa-fw fa-times"></i></a>

                <button class="btn btn-sm btn-page-header" data-toggle="tooltip" data-placement="bottom" title="Save your changes"
                        type="submit"><i class="fa fa-fw fa-check"></i></button>
        </span>

            <div class="hr"></div>
        </div>

        <div role="tabpanel">
            <div class="row">
                <div class="col-md-2">
                    <ul class="nav nav-pills nav-stacked" role="tablist">
                        <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Details</a></li>
                        <li role="presentation" class="disabled"><a href="#assign" aria-controls="security" role="tab" data-toggle="tab">Assign</a></li>
                        <li role="presentation" class="disabled"><a href="#activity" aria-controls="activity" role="tab" data-toggle="tab">Activity</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-10">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="profile">
                            <div class="form-group">
                                <label for="role_name_text" class="col-md-2 control-label">Name</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="role_name_text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description_text" class="col-md-2 control-label">Description</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="description_text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="home_view_text" class="col-md-2 control-label">Home View</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="home_view_text">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="0" id="active_ind">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade in" id="assign">
                            <p>
                                <strong>No users to assign.</strong>
                            </p>
                        </div>

                        <div role="tabpanel" class="tab-pane fade in" id="activity">
                            <p>
                                <strong>No activity recorded for this role.</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop