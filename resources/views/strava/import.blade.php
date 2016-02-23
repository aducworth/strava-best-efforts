@extends('layouts.app')

@section('content')

    <div class="page-header">
	  <h1>Importing Activities from Strava</h1>
	</div>
	<div class="panel">
           
            <div class="panel-body">
	            
	            <div id='importing-activities' class="alert" role="alert">Importing activities...</div>
	            <div id='importing-best-efforts' class="alert" role="alert" style='display: none;'>Importing best efforts...</div>
                
            </div>
        </div>
        
        <script>
        	$.ajax({
        	  url: "/strava/_get_activities"
        	}).done(function(data) {
	        	
        	    $('#importing-activities').text( "Finished Importing! " + data.imported + " activities imported." ).addClass('alert-success');
        	    $('#importing-best-efforts').show();
        	    
        	    $.ajax({
        	      url: "/strava/_get_best_efforts"
        	    }).done(function(data) {
        	      $('#importing-best-efforts').text( "Finished Importing! " + data.imported + " best efforts imported." ).addClass('alert-success');
        	    });
        	    
        	});
        </script>
 
@endsection