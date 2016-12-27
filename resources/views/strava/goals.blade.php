@extends('layouts.app')

@section('content')

	<div id='goal-vue'>

	    <div class="container">
		    
		    <h1 v-html='header'></h1>
				
			<form class="form-inline">
			  <div class="form-group">
				  {!! Form::Label('goal', 'Goal') !!}
				  <div class='col-sm-6'>
					{!! Form::text('goal', $yearly_running_goal, ['class' => 'form-control', 'placeholder' => 'Yearly Goal', 'v-model' => 'goal']) !!}
				  </div>
			  </div>
			</form>
			
			<br>
		
		</div>
		
    	<div class="page-header">
		  <h1>Goal Progress <small>Week {{ $week_of_year }}</small></h1>			  
		</div>
        <div class="panel panel-default" v-if="goal > 0">	           
            <div class="panel-body">
	            
	            <div class='row'>
		            
		            <div class='col-md-8'>
			            
			            <h2>Distance This Year <small v-html='totalDistance'></small></h2>
			            
		            </div>
		            
	            </div>
	            <div class='row'>
		            
		            <div class='col-md-8'>
			            
			            <h2>Time This Year <small>{{ $time_to_date }} hours</small></h2>
			            
		            </div>
		            
	            </div>
	            <div class='row'>
		            
		            <div class='col-md-8'>
			            
			            <h2>Weekly Distance <small v-html='weeklyDistance'></small></h2>
			            
		            </div>
		            
	            </div>
	            <div class='row'>
		            
		            <div class='col-md-8'>
			            
			            <h2>Weeks Left <small v-html='weeksLeft'></small></h2>
			            
		            </div>
		            
	            </div>
	            <div class='row'>
		            
		            <div class='col-md-8'>
			            
			            <h2>Distance Left <small v-html='distanceLeft'></small></h2>
			            
		            </div>
		            
	            </div>
	            <div class='row'>
		            
		            <div class='col-md-8'>
			            
			            <h2>Weekly Distance Needed <small v-html='weeklyGoal'></small></h2>
			            
		            </div>
		            
	            </div>
	            
            </div>
        </div>
        
		<div class="alert" role="alert" v-if="goal == 0">Please choose a goal greater than 0.</div>
		
	</div>
	
	@verbatim
	
		<script>
			
			var goalCalc = new Vue({
				el: '#goal-vue',
				data: {
					goal: <?=$yearly_running_goal?$yearly_running_goal:0 ?>,
					week: <?=$week_of_year?$week_of_year:0 ?>,
					distance: <?=$miles_to_date?$miles_to_date:0 ?>,
					units: '<?=Auth::user()->measurement_preference ?>'
				},
				watch: {
					goal: 'saveGoal'
				},
				computed: {
					header: function() {
						return ('Annual ' + (this.units == 'feet'?'Mileage':'Kilometer') + ' Goal')
					},
					totalDistance: function() {
						return formatDistance(this.distance,this.units)
					},
					weeklyDistance: function() {
						return formatDistance(this.distance/this.week,this.units)
					},
					weeksLeft: function() {
						return ((52 - this.week)>0)?(52 - this.week):1;
					},
					distanceLeft: function() {
						var goalUnits = (this.units == 'feet')?convertMeters(this.goal):(this.goal * 1000)
						var result = goalUnits-this.distance
						if(result <= 0) {
							return '-'
						} else {
							return formatDistance(goalUnits-this.distance,this.units)
						}
					},
					weeklyGoal: function() {
						var goalUnits = (this.units == 'feet')?convertMeters(this.goal):(this.goal * 1000)
						var result = (goalUnits-this.distance)/this.weeksLeft
						if(result <= 0) {
							return '-'
						} else {
							return formatDistance(result,this.units)
						}
					}
				},
				methods: {
					saveGoal: function () {
				      var xhr = new XMLHttpRequest()
				      var self = this
					  xhr.open('GET', '/strava/save-goal/' + self.goal)
					  xhr.onload = function () {
					    var response = JSON.parse(xhr.responseText)
					  }
					  xhr.send()
				    }
				}
			})
			
		</script>
		
	@endverbatim
	
	@include('strava.check')
	
@endsection