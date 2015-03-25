@if (\Auth::check())
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-dashboard">
							<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="/"><img src="/img/logo-dreamfactory-inverse.png" alt=""></a>
					</div>

					<div class="collapse navbar-collapse navbar-right" id="navbar-dashboard">
						<ul class="nav navbar-nav">
							<li><a href="http://www.dreamfactory.com/resources/" alt="Logout"><i class="fa fa-fw fa-book"></i>Resources</a></li>
							<li><a href="/auth/logout" alt="Logout"><i class="fa fa-fw fa-sign-out"></i>Logout</a></li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</nav>
@endif