<script>

$(document).ready(function(){
	
	$.ajax({
	  url: "_check_import"
	}).done(function(data) {
	  if( data.result == true ) {
		  $('#checkImport').modal();
		  var source = new EventSource("/strava/import	");
		    source.addEventListener("message", function(e)
		    {
			    var result = JSON.parse( e.data );
          
				$('.progress-bar').html(result.refresh + '%').attr('style','width: ' + result.refresh +'%;');  
				
				if( result.refresh == 100 ) {
					$( ".modal-body" ).append( '<div class="alert alert-success" role="alert">Finished importing!</div>' );
				}
				
		    }, false);
	   }
	});

});

</script>

<div id='checkImport' class="modal fade" tabindex="-1" role="dialog">
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->