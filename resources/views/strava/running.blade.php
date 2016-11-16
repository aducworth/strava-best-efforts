@extends('layouts.app')

@section('content')

	<div id='bestEfforts'>

	    <div class="container form-inline">
				
			  <div class="form-group">
			    {!! Form::select('distance', $distances, (isset($_GET['distance'])?$_GET['distance']:null), ['class' => 'form-control','placeholder' => 'Choose a Distance', 'v-model' => 'distance']) !!}
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
					            if(e.oldDate) {
					            	$bestEfforts.$data.fromDate = e.date.format('YYYY-MM-DD')
					            }
					            $('#to-date').data("DateTimePicker").minDate(e.date);
					        });
					        $("#to-date").on("dp.change", function (e) {
						        //if(e.oldDate) {
						        	$bestEfforts.$data.toDate = e.date.format('YYYY-MM-DD')
						        //}
					            $('#from-date').data("DateTimePicker").maxDate(e.date);
					        });
			            });
			        </script>
			  </div>
			
			<br>
		
		</div>
		
		@verbatim
		
    	<div class="page-header">
		  <h1>Best Efforts <small v-html="effortCount"></small></h1>
		</div>
        <div class="panel panel-default" v-if="distance != ''">
	        
           
            <div class="panel-body table-responsive">
                <table class="table table-striped activity-table">

                    <thead>
                        <th>&nbsp;</th>
                        <th @click="sortBy('elapsed_time')"
		                    :class="{ active: sortKey == 'elapsed_time' }">Time</th>
                        <th>Pace</th>
                        <th @click="sortBy('name')"
		                    :class="{ active: sortKey == 'name' }">Activity</th>
                        <th @click="sortBy('temperature')"
		                    :class="{ active: sortKey == 'temperature' }">Temperature</th>
                        <th @click="sortBy('humidity')"
		                    :class="{ active: sortKey == 'humidity' }">Humidity</th>
                        <th @click="sortBy('distance')"
		                    :class="{ active: sortKey == 'distance' }">Total Run</th>
                        <th @click="sortBy('start_date_local')"
		                    :class="{ active: sortKey == 'start_date_local' }">Date</th>
                        
                    </thead>
                    
                    <!-- Table Body -->
                    <tbody>
                        <tr v-for='entry in filteredEfforts' v-bind:class='{ today: isToday(entry.start_date_local) }'>
                            <td>{{ getIndex(entry) }}.</td>
                            <td class="table-text">
                                <div>{{ entry.elapsed_time | formatTime }}</div>
                            </td>
                            <td class="table-text">
                                <div>{{ calculatePace(entry.effort_distance,entry.elapsed_time,'<?=Auth::user()->measurement_preference ?>') }}</div>
                            </td>
                            <td class="table-text">
                                <div><a v-bind:href="getStravaId( entry.strava_id )" target='_blank'>{{ entry.name }}</a></div>
                            </td>
                            <td class="table-text">
                                <div>{{ entry.temperature | formatTemperature }}</div>
                            </td>
                            <td class="table-text">
                                <div>{{ entry.humidity }}</div>
                            </td>
                            <td class="table-text">
                                <div>{{ entry.distance | formatDistance }}</div>
                            </td>
                            <td class="table-text">
                                <div>{{ entry.start_date_local | formatDate }}</div>
                            </td>
                        </tr>                        
                    </tbody>
                </table>
            </div>
        </div>
		    
		<div class="alert" role="alert" v-if="distance == ''">Please choose a distance to view best efforts.</div>
	
	</div>
	
		<script>
			
			$bestEfforts = new Vue({
				el: '#bestEfforts',
				data: {
					distance: '',
					fromDate: '',
					toDate: '',
					effortCount: '',
					units: '<?=Auth::user()->measurement_preference ?>',
					sortKey: '',
					sortOrders: {'elapsed_time':1,'name':1,'temperature':1,'humidity':1,'distance':1,'start_date_local':1},
					efforts: {},
					orderedEfforts: {}
				},
				created: function () {
					this.fetchData()
				},
				computed: {
					filteredEfforts: function() {
						var sortKey = this.sortKey
						var order = this.sortOrders[sortKey] || 1
						var distance = this.distance
						var fromDate = this.fromDate
						var toDate = this.toDate
						var data = this.efforts
						
						if(distance) {
							data = data.filter(function (row) {
					          return row['effort_name'] == distance
					        })
						}
						
						if(fromDate) {
							data = data.filter(function (row) {
					          return moment(row['start_date_local']).isAfter(fromDate)
					        })
						}
						if(toDate) {
							data = data.filter(function (row) {
					          return moment(row['start_date_local']).isBefore(moment(toDate).add(1,'days'))
					        })
						}
						
						// set ordered efforts to maintain index
						this.orderedEfforts[distance] = data
						
						if (sortKey) {
							data = data.slice().sort(function (a, b) {
							  a = a[sortKey]
							  b = b[sortKey]
							  return (a === b ? 0 : a > b ? 1 : -1) * order
							})
						}
						
						this.effortCount = data.length
						return data
					}
				},
				filters: {
					formatDate: formatDate,
					formatDistance: function(distance) {
						return formatDistance(distance,'<?=Auth::user()->measurement_preference ?>')
					},
					formatTime: formatTime,
					formatTemperature: function(temperature) {
						return formatTemperature(temperature,'<?=Auth::user()->measurement_preference ?>')
					}
				},
				methods: {
					fetchData: function() {
						var xhr = new XMLHttpRequest()
						var self = this
						xhr.open('GET', '/strava/get-best-efforts')
						xhr.onload = function () {
							self.efforts = JSON.parse(xhr.responseText)
							console.log(self.efforts[0].name)
						}
						xhr.send()
					},
					getStravaId: function(strava_id) {
						return 'https://www.strava.com/activities/' + strava_id;
					},
					sortBy: function(key) {
						this.sortKey = key
						this.sortOrders[key] = this.sortOrders[key] * -1
					},
					getIndex: function(entry) {
						return this.orderedEfforts[this.distance].indexOf(entry) + 1
					}
				}
			})
			
		</script>
			
	@endverbatim
	
	@include('strava.check')
	
@endsection