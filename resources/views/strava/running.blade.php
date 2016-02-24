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
	
	@if (isset($_GET['distance']))
			
	    @if (count($efforts) > 0)
	    	<div class="page-header">
			  <h1><?=(isset($_GET['distance'])?($_GET['distance'].' '):'') ?>Best Efforts <small>{{ count($efforts) }}</small></h1>
			</div>
	        <div class="panel panel-default">
		        
	           
	            <div class="panel-body">
	                <table class="table table-striped activity-table">
	
	                    <!-- Table Headings -->
	                    <thead>
	                        <th>&nbsp;</th>
	                        <th>Time</th>
	                        <th>Date</th>
	                        <th>&nbsp;</th>
	                    </thead>
	                    
	                    <? $i = 1; ?>
	                    
	                    <!-- Table Body -->
	                    <tbody>
	                        @foreach ($efforts as $effort)
	                            <tr>
		                            <td>{{ $i }}.</td>
	                                <td class="table-text">
	                                    <div>{{ gmdate('H:i:s', $effort->moving_time) }}</div>
	                                </td>
	                                <td class="table-text">
	                                    <div>{{ date('m/d/Y g:ia', strtotime( $effort->start_date_local )) }}</div>
	                                </td>
	
	                                <td>
	                                    <!-- TODO: Delete Button -->
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
	
	<div class="alert alert-info" role="alert">Please choose a distance to view best efforts.</div>
	
	@endif
	
@endsection