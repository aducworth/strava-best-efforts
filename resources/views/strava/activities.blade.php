@extends('layouts.app')

@section('content')

	<div class="container">
			
		<form class="form-inline">
		  <div class="form-group">
		    {!! Form::select('type', $types, (isset($_GET['type'])?$_GET['type']:null), ['class' => 'form-control','placeholder' => 'Choose a Type']) !!}
		  </div>
		  <div class="form-group">
			  {!! Form::Label('from_date', 'From') !!}
			  <div class='col-sm-6'>
				{!! Form::text('from_date', (isset($_GET['from_date'])?$_GET['from_date']:null), ['class' => 'form-control','id' => 'from-date','placeholder' => 'From']) !!}
			  </div>
		  </div>
		  <div class="form-group">
			  {!! Form::Label('to_date', 'To') !!}
			  <div class='col-sm-6'>
				{!! Form::text('to_date', (isset($_GET['to_date'])?$_GET['to_date']:null), ['class' => 'form-control','id' => 'to-date','placeholder' => 'To']) !!}
			  </div>
				<script type="text/javascript">
		            $(function () {
			            $('#from-date').datetimepicker({'format':'MM/DD/YYYY'});
			            
		                $('#to-date').datetimepicker({
			                'format':'MM/DD/YYYY',
			                useCurrent: false //Important! See issue #1075
			             });
			             
			             $("#from-date").on("dp.change", function (e) {
				            $('#to-date').data("DateTimePicker").minDate(e.date);
				        });
				        $("#to-date").on("dp.change", function (e) {
				            $('#from-date').data("DateTimePicker").maxDate(e.date);
				        });
		            });
		        </script>
		  </div>
		  <button type="submit" class="btn btn-default">Filter</button>
		</form>
		
		<br>
	
	</div>

    <!-- Current Activities -->
    @if (count($activities) > 0)
    	<div class="page-header">
		  <h1>Activities <small>{{ count($activities) }}</small></h1>
		</div>
        <div class="panel panel-default">
	        
           <form action='/strava/multi' class="form-inline">
	           
	            <div class="panel-body table-responsive">
	                <table class="table table-striped activity-table">
	
	                    <!-- Table Headings -->
	                    <thead>
		                    <th><input type='checkbox' id='check-all'></th>
	                        <th>Activity</th>
	                        <th>Type</th>
	                        <th>Distance</th>
	                        <th>Time</th>
	                        <th>Temperature</th>
	                        <th>Humidity</th>
	                        <th>Date</th>
	                        <th>&nbsp;</th>
	                    </thead>
	
	                    <!-- Table Body -->
	                    <tbody>
		                    
		                    <?
			                    $total_distance = 0;
			                    $total_time		= 0;
			                ?>
			                
	                        @foreach ($activities as $activity)
	                            <? 
		                        	$today = false;
		                        	
		                        	if( date('Y-m-d',strtotime($activity->start_date_local)) == date('Y-m-d') ) {
			                        	$today = true;
		                        	}
		                        	
		                        	 $total_distance 	+= $activity->distance;
									 $total_time		+= $activity->moving_time;
									 
		                        ?>
	                            <tr <? if($today): ?>class='today'<? endif; ?>>  
		                            <td><input type='checkbox' name='multi_edit[]' class='multi-edit' value='{{ $activity->strava_id }}'></td>                              
			                        <td class="table-text">
	                                    <div><a href='https://www.strava.com/activities/{{ $activity->strava_id }}' target='_blank'>{{ $activity->name }}</a></div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ $activity->type }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatDistance( $activity->distance ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatTime( $activity->moving_time ) }}</div>
	                                </td>
	                                
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatTemp( $activity->temperature ) }}</div>
	                                </td>
									<td class="table-text">
	                                    <div>{{ $activity->humidity }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatDate( $activity->start_date_local ) }}</div>
	                                </td>
	                                <td>
	                                    <!-- TODO: Delete Button -->
	                                </td>
	                            </tr>
	                        @endforeach
	                    </tbody>
	                    <!-- Table Headings -->
	                    <thead>
		                    <th>&nbsp;</th>
	                        <th>Totals</th>
	                        <th>&nbsp;</th>
	                        <th>{{ App\Activity::formatDistance( $total_distance ) }}</th>
	                        <th>{{ App\Activity::formatTime( $total_time ) }}</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                    </thead>
	                </table>
	                
	                <div class='row'>
		            
			            <div class='col-sm-6'>
		            
							<div class="form-group">
								
								<input type='hidden' name='type' value='<?=(isset($_GET['type'])?$_GET['type']:'') ?>'/>
								<input type='hidden' name='from_date' value='<?=(isset($_GET['from_date'])?$_GET['from_date']:'') ?>'/>
								<input type='hidden' name='to_date' value='<?=(isset($_GET['to_date'])?$_GET['to_date']:'') ?>'/>
								
								<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete Selected</button>						
							</div>
						
			            </div>
					
		            </div>
		            
	            </div>
            
           </form>
           
        </div>
        
    @else
	    
    	<div class="alert alert-info" role="alert">No activites match the search criteria.</div>
    	
    @endif
    
    <script type="text/javascript">
	    
		$("#check-all").on('change', function () {
		    $(".multi-edit").prop('checked', $(this).prop("checked"));
		});
	</script>
    
    @include('strava.check')
	
@endsection