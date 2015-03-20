@extends('layouts.dark-page')

@section('page-title')
    Login
@overwrite

@section('head-links')
    @parent
    <link href="/css/login.css" rel="stylesheet">
    <link href="/css/metro.css" rel="stylesheet">
@stop

@section('content')
    <div id="container-login" class="container-fluid">
        <div class="row">
            <div class="col-md-offset-4 col-md-4 col-md-offset-4">
                <div class="container-logo">
                    <h3><img src="/img/logo-dfe.png" alt="" />
                        <small>DreamFactory Enterprise <span>{{ \Config::get('dfe.version') }}</span>
                        </small>
                    </h3>
                </div>

                @if (count($errors) > 0)
                    <div class="alert alert-warning alert-dismissible alert-rounded fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4>Rut-roh...</h4>

                        <ul style="list-style: none">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="form-horizontal" role="form" method="POST" action="/password/reset">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon bg_lg"><i class="fa fa-user"></i></span>

                            <input type="email"
                                   class="form-control email required"
                                   autofocus
                                   name="email"
                                   placeholder="email address"
                                   value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon bg_ly"><i class="fa fa-lock"></i></span>

                            <input class="form-control password required" placeholder="new password" name="password" type="password" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon bg_ly"><i class="fa fa-lock"></i></span>

                            <input class="form-control password required" placeholder="new password (again)" name="password_confirmation" type="password" />
                        </div>
                    </div>

                    <label class="control-label sr-only" for="email">Email Address</label>
                    <label class="control-label sr-only" for="password">Password</label>
                    <label class="control-label sr-only" for="password_confirmation">Password Confirmation</label>

                    <div class="form-actions">
                        <span class="pull-right"><button type="submit" class="btn btn-danger">Reset Password</button></span>
                    </div>

                </form>

            </div>
        </div>
    </div>
@stop

@section( 'after-app-scripts' )
    @parent
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
    <script src="/js/validate.js"></script>
    <script src="/js/login.js"></script>
@stop