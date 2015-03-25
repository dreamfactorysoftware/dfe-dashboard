<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('page-title', 'Welcome!') | DreamFactory Enterprise&trade;</title>
	@section('page-theme')
		<link href="/vendor/dfe-common/static/bootswatch-3.3.4/flatly.min.css" rel="stylesheet">
	@show
	<link href="/static/font-awesome-4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="/css/style.css" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="//oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
	<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->

	@section('head-links')
	@show

	@section('head-scripts')
	@show
</head>
<body class="@yield('body-class')">

@include('layouts.partials.navbar')

<div id="page-content" class="container-fluid container-content">
	@yield('content')
</div>

<div class="footer">
	<div class="container-fluid">
		<div class="social-links pull-right">
			<ul class="list-inline">
				<li>
					<a target="_blank"
					   href="http://facebook.com/dfsoftwareinc"><i class="fa fa fa-facebook-square fa-2x"></i></a>
				</li>
				<li>
					<a target="_blank"
					   href="https://twitter.com/dfsoftwareinc"><i class="fa fa-twitter-square fa-2x"></i></a>
				</li>
				<li>
					<a target="_blank"
					   href="http://dreamfactorysoftware.github.io/"><i class="fa fa-github-square fa-2x"></i></a>
				</li>
			</ul>
		</div>
		<div class="clearfix"></div>
		<p>
			<span class="pull-left hidden-xs hidden-sm">DreamFactory Enterprise&trade; Dashboard<small style="margin-left: 5px;font-size: 9px;">(v1.2.7)</small></span>
			<span class="pull-right">&copy; DreamFactory Software, Inc. 2012-<?php echo date( 'Y' ); ?>.&nbsp;All Rights Reserved.</span>
		</p>
	</div>
</div>

@section('before-body-scripts')
@show

<script src="/static/jquery-2.1.3/jquery.min.js"></script>
<script src="/static/bootstrap-3.3.4/js/bootstrap.min.js"></script>

@section('after-body-scripts')
@show
</body>
</html>
