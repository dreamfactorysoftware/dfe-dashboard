<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('page-title', 'Welcome!') | {!! config('dfe.common.display-name') !!}</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/apple-touch-icon-120x120.png">
    <link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/img/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/img/manifest.json">
    @section('page-theme')<link href="/static/bootswatch-3.3.5/flatly.min.css" rel="stylesheet">@show
    <link href="/static/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Oswald|Montserrat' rel='stylesheet' type='text/css'>
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/partner.css" rel="stylesheet">
    <script src="/static/jquery-2.1.4/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->

    @section('head-links')
    @show

    @section('head-scripts')
    @show
    <script type="text/javascript">
        var _rcCallback = function () {
            var _gr = {
                'sitekey': '{{ config('recaptcha.siteKey') }}',
                'theme': 'light'
            };

            $('.g-recaptcha').each(function () {
                var _id = $(this).attr('id');

                if (_id) {
                    grecaptcha.render(_id, _gr);
                }
            });
        };
    </script>
</head>
<body class="@yield('body-class')">

@include('layouts.partials.navbar')

<div class="container-fluid page-content">
    @yield('content')
</div>

<div class="footer">
    <div class="container-fluid">
        <div class="social-links pull-right">
            <ul class="list-inline">
                <li>
                    <a target="_blank"
                       href="https://github.com/dreamfactorysoftware/"><i class="fa fa-github-square fa-2x"></i></a>
                </li>
                <li>
                    <a target="_blank"
                       href="https://facebook.com/dfsoftwareinc/"><i class="fa fa fa-facebook-square fa-2x"></i></a>
                </li>
                <li>
                    <a target="_blank"
                       href="https://twitter.com/dfsoftwareinc/"><i class="fa fa-twitter-square fa-2x"></i></a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
        <p>
            <span class="pull-left hidden-xs hidden-sm">{!! config( 'dfe.common.display-name' ) !!}
                <small style="margin-left: 5px;font-size: 9px;">({!! config('dfe.common.display-version') !!})
                </small></span>
            <span class="pull-right">{!! config('dfe.common.display-copyright') !!}</span>
        </p>
    </div>
</div>

@section('before-body-scripts')
@show

<script src="https://www.google.com/recaptcha/api.js?onload=_rcCallback&render=explicit" async defer></script>
<script src="/static/bootstrap-3.3.5/js/bootstrap.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js"></script>
<script src="/js/app.jquery.js"></script>
<script src="/js/instance.validate.js"></script>
<script src="/js/df.dashboard.js"></script>

@section('after-body-scripts')
@show
</body>
</html>
