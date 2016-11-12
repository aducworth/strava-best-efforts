@extends('layouts.app')

@section('content')

<div class="container">
    
<h1 class="rte-heading">Frequent Questions</h1>
<div class="row">
  <div class="rte col-md-10 col-sm-12 col-sm-offset-0">
	  
    <h4 class="p1">Simple answers to your most common&nbsp;questions<br><br></h4>
    
		<p class="p1">
			
			<span class="s1"><b>Can I re-import some of my activities that seem out of sync?<br><br></b></span>
			
			Yes, activities sometimes get out of sync due to communication issues with Strava. To re-import activities, take the following steps:

			<ol>
				<li>Go to <a href='/strava/activities'>Activities</a> and search for all activities ( not just runs ) that have happened since you started having problems.</li>
				<li>Check the box at the top of the table to check all activities</li>
				<li>Scroll down to the bottom of the page and click "Delete Selected"</li>
				<li>After they have been removed, click your name in the upper right menu and click "Import from Strava"</li>
				<li>If you are still having issues, submit a support request to have your account completely reset.</li>
			</ol>

		</p>
		
		<p class="p1">
			
			<br><br>
			
			<span class="s1"><b>How do I get weather data for my activities?<br><br></b></span>
			
			Weather data is only available for current weather conditions, so the only way to get weather data is to import your activities on the same day they happen. Weather integration was added on November 10, 2016, so no activities before that date will have weather data.

		</p>
		
		<p class="p1">
			
			<br><br>
			
			<span class="s1"><b>Why didn’t some of my best efforts get recorded on a run?<br><br></b></span>
			
			That’s a good question. Strava doesn’t pick them up sometimes, and I’m not sure why. I think it may have to do with auto-pausing during an activity.


		</p>
		
		<p class="p1">
			
			<br><br>
			
			<span class="s1"><b>Can I offer suggestions for improving the site?<br><br></b></span>
			
			Yes! Click on the Support link in the menu to the right and send me a note.


		</p>
		
  </div>
</div>

  </div>
  
 @endsection