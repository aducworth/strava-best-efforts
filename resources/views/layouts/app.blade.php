<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>View All Estimated Best Efforts From Runs - Strava Best Efforts</title>
	
	<script type="text/javascript" src="{{ asset("assets/bower_components/jquery/dist/jquery.min.js") }}"></script>
	<script type="text/javascript" src="{{ asset("assets/bower_components/moment/min/moment.min.js") }}"></script>
	<script type="text/javascript" src="{{ asset("assets/bower_components/bootstrap/dist/js/bootstrap.min.js") }}"></script>
	<script type="text/javascript" src="{{ asset("assets/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js") }}"></script>
	<link rel="stylesheet" href="{{ asset("assets/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css") }}" />
	<script src="https://code.highcharts.com/highcharts.js"></script>
    <link href="{{ asset("assets/css/app.css") }}" rel="stylesheet">
    
    <style>
	    body { padding-top: 70px; }
	</style>

</head>

<body>
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
						@if (!Auth::guest())
							<li role="presentation" class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
									{{ Auth::user()->name }} <span class="caret"></span>
    							</a>
    							<ul class="dropdown-menu">
	    							<li><a id='manualImport' href="#"><i class="fa fa-btn"></i>Import from Strava</a></li>
	    							<li><a id='supportRequest' href="#"><i class="fa fa-btn"></i>Support</a></li>
	    							<li><a href="/auth/logout"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
    							</ul>
							</li>	
						@endif
					</ul>
				</div>
			</div>
		</nav>
	<div class="container">
		@yield('content')
	</div>
	
	<script>
				
		$(document).ready(function(){
		
			$('#manualImport').click(function(e){
				e.preventDefault();
				stravaImport();
			});
			
			$('#supportRequest').click(function(e){
				e.preventDefault();
				
				$('#supportSuccess').hide();
				$('#supportFailure').hide();
				
				//$('#supportModal').modal();
				
				$.ajax({
				  url: "/_supportform"
				}).done(function(data) {
				  $('#supportForm').html( data );
				  $('#supportModal').modal();
				});
			});
			
			$('#supportForm').submit(function(e){
				e.preventDefault();
				
				var postData = $(this).serialize() + '&url=' + $('#url').val();
				
				$.ajax({
				  url: "/_support",
				  data: postData,
				  type: "POST"
				}).done(function(data) {
					
				  if( data.result ) {
					  $('#supportSuccess').show();
				  } else {
					  $('#supportFailure').text(data.errors).show();
				  }
				});
				
			});
			
		});
		
		function stravaImport() {
			
			  $('#importSuccess').hide();
			  $('#importFailure').hide();
			  $('.progress-bar').html('0%').attr('style','width: 0%;');
			   
			  $('#importModal').modal();
			  var source = new EventSource("/strava/import	");
			    source.addEventListener("message", function(e)
			    {
				    var result = JSON.parse( e.data );
	          
					if( result.refresh != null ) {
						$('.progress-bar').html(result.refresh + '%').attr('style','width: ' + result.refresh +'%;');  
					
						if( result.refresh == 100 ) {
							$('#importSuccess').show();
						}
					}
					
					if( result.message != null ) {
						
						$('#importFailure').html('Errors: ' + result.message + '. Please try again in 15 minutes.').show();
						
					}
					
					
			    }, false);
		}
	
	</script>
	
	<div id='importModal' class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Importing from Strava</h4>
	      </div>
	      <div class="modal-body">
		    <p>Importing all of your activities and best efforts. This will only take a second.</p>
	        <div class="progress">
			  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
			    0%
			  </div>
			</div>
			<div id='importSuccess' class="alert alert-success" style='display: none;' role="alert">Finished importing!</div>
			<div id='importFailure' class="alert alert-danger" style='display: none;' role="alert"></div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div id='supportModal' class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Send Support Request</h4>
	      </div>
	      <div class="modal-body">
		    <div id='supportSuccess' class="alert alert-success" style='display: none;' role="alert">We have received your request, and we'll get back to you soon!</div>
			<div id='supportFailure' class="alert alert-danger" style='display: none;' role="alert"></div>
			
			<input type="hidden" id="url" value="<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>">
			
		    <form id="supportForm" action="/_support" method='post'>
			    
			</form>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
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