@extends('layouts.app')

@section('content')

    <div class="container">
			
		<form class="form-inline">
		  <div class="form-group">
			  <div class='col-sm-6'>
				<select class='form-control' name='split_count'>
					<? for( $i=1; $i < 31; $i++ ): ?>
						<option value='<?=$i ?>' <?=($split_count==$i)?"selected":'' ?>><?=$i ?> Splits</option>
					<? endfor; ?>
				</select>
			  </div>
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
	
	@if ((isset($_GET['to_date']) && $_GET['to_date']) || (isset($_GET['from_date']) && $_GET['from_date']))
			
	    @if (count($runs) > 0)
	    	<div class="page-header">
			  <h1>Analyze <?=(Auth::user()->measurement_preference == 'feet')?'Mile':'Kilometer' ?> Splits <small>{{ count($runs) }}</small></h1>
			</div>
	        <div class="panel panel-default">
		        
	           
	            <div class="panel-body">
	                <table class="table table-striped activity-table">
	
	                    <thead>
	                        <th>&nbsp;</th>
	                        <th>Time</th>
	                        <th>Pace</th>
	                        <th>Activity</th>
	                        <th>Total Run</th>
	                        <th>Date</th>
	                        
	                        @for($split = 1;$split < ($split_count+1);$split++)
	                        
	                        	<th>Split {{ $split }}</th>
	                        	
	                        @endfor
	                        
	                    </thead>
	                    
	                    <? $i = 1; ?>
	                    
	                    <!-- Table Body -->
	                    <tbody>
	                        @foreach ($runs as $run)
	                        	<? 
		                        	$today = false;
		                        	
		                        	if( date('Y-m-d',strtotime($run->start_date_local)) == date('Y-m-d') ) {
			                        	$today = true;
		                        	}
		                        ?>
	                            <tr <? if($today): ?>class='today'<? endif; ?>>
		                            <td>{{ $i }}.</td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatTime( $run->elapsed_time ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::calculatePace( $run->distance, $run->elapsed_time ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div><a href='https://www.strava.com/activities/{{ $run->strava_id }}' target='_blank'>{{ $run->name }}</a></div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatDistance( $run->distance ) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ App\Activity::formatDate( $run->start_date_local ) }}</div>
	                                </td>
	                                
	                                <? $these_splits = 0; ?>
	                                
	                                <? $run_splits = $run->splits()->where('type','standard')->orderBy('split','asc')->get(); ?>
	                                					
	                                <? foreach( $run_splits as $ind_split ): ?>
	                                					
										<td class="table-text">
		                                    <div>
			                                    
			                                    	<? //$ind_split->split ?>
			                                    	{{ App\Activity::formatTime( $ind_split->elapsed_time ) }}
			                                    
			                                </div>
		                                </td>
		                                
		                                <? $these_splits++; ?>
		                                
		                                <? if( $these_splits >= $split_count ) { break; } ?>
		                                
		                           <? endforeach; ?>
		                           
		                           <? for($split = $these_splits;$split < ($split_count+1);$split++): ?>
	                        
			                        	<td class="table-text">
		                                    <div>&nbsp;</div>
		                                </td>
			                        	
			                        <? endfor; ?>          
			                        		
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