<script>

$(document).ready(function(){
	
	$.ajax({
	  url: "_check_import"
	}).done(function(data) {
	  if( data.result == true ) {
		  stravaImport();
	   }
	});

});

</script>