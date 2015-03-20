@extends('layouts.dashboard-page')

@section('body-class')
    error-wrapper
@stop

@section('head')
    Oops!
@stop

@section('partials')
    <div id="wrapper">
        @include('layouts.main.navbar')

        <div id="page-wrapper" class="container-fluid">
            <div class="row">
                <?php echo renderBreadcrumbs( array('Dashboard' => false), false ); ?>
            </div>
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">@yield('page-header','Nada')
                        <small>@yield('page-subheader','me so empty!')</small>
                    </h1>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
@overwrite
