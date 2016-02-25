@extends('layouts.app')

@section('content')

	<div class="page-header">
	  <h1>Stats</h1>
	</div>
		
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					Best Effort Stats
				</div>

				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')
					
					<div class="row">
					  <div class="col-md-4">.col-md-4</div>
					  <div class="col-md-8">
						  <div id='stat-chart' style='min-width: 600px;
    height: 400px;
    margin: 0 auto;'></div>	
					  </div>
					</div>

									
<!--
					
-->			
									
				</div>
			</div>
		</div>
	</div>
	
	<script>

$(function () {
    $('#stat-chart').highcharts({
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Monthly Stats'
        },
        subtitle: {
            text: 'Source: strava.com'
        },
        xAxis: [{
            categories: [<?=$months ?>],
            crosshair: true
        }],
        yAxis: [
        { // Primary yAxis
            type: 'datetime',
            dateTimeLabelFormats: { //force all formats to be hour:minute:second
               second: '%H:%M:%S',
               minute: '%H:%M:%S',
               hour: '%H:%M:%S',
               day: '%H:%M:%S',
               week: '%H:%M:%S',
               month: '%H:%M:%S',
               year: '%H:%M:%S'
            }, 
            title: {
                text: 'Time',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            opposite: true

        }/*
, 
        { // Secondary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Rainfall',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} mm',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            }

        }, { // Tertiary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Sea-Level Pressure',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            labels: {
                format: '{value} mb',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            opposite: true
        }
*/],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 80,
            verticalAlign: 'top',
            y: 55,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [/*
{
            name: 'Rainfall',
            type: 'column',
            yAxis: 1,
            data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
            tooltip: {
                valueSuffix: ' mm'
            }

        }, {
            name: 'Sea-Level Pressure',
            type: 'spline',
            yAxis: 2,
            data: [1016, 1016, 1015.9, 1015.5, 1012.3, 1009.5, 1009.6, 1010.2, 1013.1, 1016.9, 1018.2, 1016.7],
            marker: {
                enabled: false
            },
            dashStyle: 'shortdot',
            tooltip: {
                valueSuffix: ' mb'
            }

        }, 
*/
		<? foreach( $distances as $distance => $output ): ?>
		{
            name: '<?=$distance ?>',
            type: 'spline',
            data: [<?=$output ?>],
            tooltip: {
                valueSuffix: ' min'
            }
        },
        <? endforeach; ?>]
    });
});
</script>

@endsection