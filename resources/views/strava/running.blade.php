@extends('layouts.app')

@section('content')

    <div class="container">
			
		<form class="form-inline">
		  <div class="form-group">
		    {!! Form::select('distance', $distances, (isset($_GET['distance'])?$_GET['distance']:null), ['class' => 'form-control','placeholder' => 'Choose a Distance']) !!}
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
	
	@if (isset($_GET['distance']) && $_GET['distance'])
			
	    @if (count($efforts) > 0)
	    	<div class="page-header">
			  <h1><?=(isset($_GET['distance'])?($_GET['distance'].' '):'') ?>Best Efforts <small>{{ count($efforts) }}</small></h1>
			</div>
	        <div class="panel panel-default">
		        
	           
	            <div class="panel-body table-responsive">
	                <table class="table table-striped activity-table">
	
	                    <thead>
	                        <th>&nbsp;</th>
	                        <th>Time</th>
	                        <th>Pace</th>
	                        <th>Activity</th>
	                        <th>Temperature</th>
	                        <th>Humidity</th>
	                        <th>Total Run</th>
	                        <th>Date</th>
	                        
	                    </thead>
	                    
	                    <? $i = 1; ?>
	                    
	                    <!-- Table Body -->
	                    <tbody>
	                        @foreach ($efforts as $effort)
	                        	<? 
		                        	$today = false;
		                        	
		                        	if( date('Y-m-d',strtotime($effort->start_date_local)) == date('Y-m-d') ) {
			                        	$today = true;
		                        	}
		                        ?>
	                            <tr <? if($today): ?>class='today'<? endif; ?>>
		                            <td>{{ $i }}.</td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatTime( $effort->elapsed_time ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::calculatePace( $effort->effort_distance, $effort->elapsed_time ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div><a href='https://www.strava.com/activities/{{ $effort->strava_id }}' target='_blank'>{{ $effort->name }}</a></div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatTemp( $effort->temperature ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ $effort->humidity }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatDistance( $effort->distance ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatDate( $effort->start_date_local ) }}</div>
	                                </td>
	
	                            </tr>
	                            
	                            <? $i++; ?>
	                            
	                        @endforeach
	                        
	                    </tbody>
	                </table>
	            </div>
	        </div>
	    @else
	    
	    	<div class="alert alert-info" role="alert">No best efforts are currently in the database.</div>
	    	
	    @endif
	    
	@else
	
	<div class="alert" role="alert">Please choose a distance to view best efforts.</div>
	
	@endif
	
	@include('strava.check')
	
@endsection