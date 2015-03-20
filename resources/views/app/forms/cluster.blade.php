@extends('layouts.main')

@section('content')
    <form class="form-horizontal" action="/api/v1/clusters" method="POST">
        <div class="page-heading">
            <h3 class="page-header pull-left">{{ $pageHeader }}</h3>
                <span class="pull-right">
                <a class="btn btn-sm btn-page-header" data-toggle="tooltip" data-placement="bottom" title="Discard any changes"
                   href="/settings/clusters"><i class="fa fa-fw fa-times"></i></a>

                <button class="btn btn-sm btn-page-header" data-toggle="tooltip" data-placement="bottom" title="Save your changes"
                        type="submit"><i class="fa fa-fw fa-check"></i></button>
        </span>

            <div class="hr"></div>
        </div>

        <div role="tabpanel">
            <div class="row">
                <div class="col-md-2" style="margin-top: 20px;">
                    <ul class="nav nav-pills nav-stacked" role="tablist">
                        <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Details</a></li>
                        <li role="presentation"><a href="#assign" aria-controls="security" role="tab" data-toggle="tab">Assign</a></li>
                        <li role="presentation"><a href="#activity" aria-controls="activity" role="tab" data-toggle="tab">Activity</a></li>
                    </ul>
                </div>
                <div class="col-md-10">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="profile">
                            <div class="form-group">
                                <label for="cluster_id_text" class="col-md-2 control-label">Name</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="cluster_id_text">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="subdomain_text" class="col-md-2 control-label">Subdomain</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="subdomain_text">
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade in" id="assign">
                            <p>
                                <strong>No servers to assign.</strong>
                            </p>
                        </div>

                        <div role="tabpanel" class="tab-pane fade in" id="activity">
                            <p>
                                <strong>No activity recorded for this cluster.</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop