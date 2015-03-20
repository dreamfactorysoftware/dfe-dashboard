<!DOCTYPE html >
<html lang="en">
<head>
    @include('layouts.partials.head-master')

    @section('head-links')
    @show

    @section('head-scripts')
    @show
</head>
<body class="@yield('body-class')">

@yield('content')

@section('before-body-scripts')
@show

@include('layouts.partials.body-scripts-main')

@section('before-app-scripts')
@show

<script src="/js/EnterpriseServer.js"></script>
<script src="/js/cerberus.js"></script>

@section('after-app-scripts')
@show

</body>
</html>
