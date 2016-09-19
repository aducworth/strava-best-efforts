@extends('layouts.app')

@section('content')

    <div class="container">
	    
	    <h1>Annual <?=(Auth::user()->measurement_preference == 'feet')?'Mileage':'Kilometer' ?> Goal</h1>
			
		<form class="form-inline">
		  <div class="form-group">
			  {!! Form::Label('goal', 'Goal') !!}
			  <div class='col-sm-6'>
				{!! Form::text('goal', $yearly_running_goal, ['class' => 'form-control', 'placeholder' => 'Yearly Goal']) !!}
			  </div>
		  </div>
		  <button type="submit" class="btn btn-default">Update</button>
		</form>
		
		<br>
	
	</div>
	
	@if ( $yearly_running_goal > 0 )
			
	    	<div class="page-header">
			  <h1>Goal Progress <small>Week {{ $week_of_year }}</small></h1>			  
			</div>
	        <div class="panel panel-default">	           
	            <div class="panel-body">
		            
		            <div class='row'>
			            
			            <div class='col-md-8'>
				            
				            <h2>Distance This Year <small>{{ $miles_to_date }}</small></h2>
				            
			            </div>
			            
		            </div>
		            <div class='row'>
			            
			            <div class='col-md-8'>
				            
				            <h2>Time This Year <small>{{ $time_to_date }} hours</small></h2>
				            
			            </div>
			            
		            </div>
		            <div class='row'>
			            
			            <div class='col-md-8'>
				            
				            <h2>Weekly Distance <small>{{ $weekly_mileage }}</small></h2>
				            
			            </div>
			            
		            </div>
		            <div class='row'>
			            
			            <div class='col-md-8'>
				            
				            <h2>Weeks Left <small>{{ $weeks_left }}</small></h2>
				            
			            </div>
			            
		            </div>
		            <div class='row'>
			            
			            <div class='col-md-8'>
				            
				            <h2>Distance Left <small>{{ $miles_to_go }}</small></h2>
				            
			            </div>
			            
		            </div>
		            <div class='row'>
			            
			            <div class='col-md-8'>
				            
				            <h2>Weekly Distance Needed <small>{{ $weekly_goal }}</small></h2>
				            
			            </div>
			            
		            </div>
		            
	            </div>
	        </div>
	        	    
	@else
	
	<div class="alert" role="alert">Please choose a goal greater than 0.</div>
	
	@endif
	
	@include('strava.check')
	
@endsection