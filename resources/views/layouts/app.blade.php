<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Strava Best Efforts</title>
	
	<script type="text/javascript" src="{{ asset("assets/bower_components/jquery/dist/jquery.min.js") }}"></script>
	<script type="text/javascript" src="{{ asset("assets/bower_components/moment/min/moment.min.js") }}"></script>
	<script type="text/javascript" src="{{ asset("assets/bower_components/bootstrap/dist/js/bootstrap.min.js") }}"></script>
	<script type="text/javascript" src="{{ asset("assets/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js") }}"></script>
	<link rel="stylesheet" href="{{ asset("assets/bower_components/bootstrap/dist/css/bootstrap.min.css") }}" />
	<link href="{{ asset("assets/css/bootstrap.css") }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset("assets/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css") }}" />
	<script src="https://code.highcharts.com/highcharts.js"></script>

    <link href="{{ asset("assets/css/bootstrap.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/css/app.css") }}" rel="stylesheet">

</head>

<body>
<!-- 	<div class="container"> -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<a class="navbar-brand" href="/">Strava Best Efforts</a>
				</div>

				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						@if (!Auth::guest())
							<li><a href="/strava/activities"><i class="fa fa-btn fa-heart"></i>Activities</a></li>
							<li><a href="/strava/running"><i class="fa fa-btn fa-heart"></i>Running</a></li>
						@endif
					</ul>

					<ul class="nav navbar-nav navbar-right">
						@if (Auth::guest())
<!--
							<li><a href="/auth/register"><i class="fa fa-btn fa-heart"></i>Register</a></li>
							<li><a href="/auth/login"><i class="fa fa-btn fa-sign-in"></i>Login</a></li>
-->
						@else
							<li><a href="/strava/profile"><i class="fa fa-btn fa-user"></i>{{ Auth::user()->name }}</a></li>
							<li><a href="/auth/logout"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
						@endif
					</ul>
				</div>
			</div>
		</nav>
<!-- 	</div> -->
	<div class="container">
		@yield('content')
	</div>
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-74182568-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
</body>
</html>