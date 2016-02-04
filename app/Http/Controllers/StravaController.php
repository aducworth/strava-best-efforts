<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Iamstuartwilson;

class StravaController extends Controller
{
	
	var $clientId;
	var $clientSecret;
	var $api;
	
	public function __construct(Request $request = null) {
		
		$this->middleware('auth');
		
		$this->clientId = getenv('STRAVA_CLIENT_ID');
		$this->clientSecret = getenv('STRAVA_CLIENT_SECRET');
		
		$this->api = new Iamstuartwilson\StravaApi(
		    $this->clientId,
		    $this->clientSecret
		);
		
		if( $request->user()->strava_token ) {
			$this->api->setAccessToken( $request->user()->strava_token );
		}	
		
	}
	
	/**
	 * Display link to connect to Strava.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function connect(Request $request)
	{
	    $url = $this->api->authenticationUrl('http://localhost/strava/authenticate', 'auto', 'write', 'mystate');
	
	    return view('strava.connect', [
	        'url' => $url,
	    ]);
	}
	
	/**
	 * authenticate
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function authenticate(Request $request)
	{
		$code = $request->input('code');
		
		echo( $code );
		
		if( $code ) {
			$token = $this->api->tokenExchange($code);
			print_r( $token );
			$request->user()->strava_token = $token->access_token;
			echo( $request->user()->save() );
		}
		
	}
	
    /**
	 * Get activities
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function activities(Request $request)
	{
		$page 			= 1;
		$activity_count = 200;
		
		while( $activity_count == 200 && $page < 10 ) {
		
			$activities = $this->api->get('athlete/activities', ['per_page' => 200,'page' => $page]);
			
			foreach( $activities as $activity ) {
				
				//print_r( $activity );
				
				$data = array(
								'strava_id'				=> $activity->id,
								'athlete' 				=> $activity->athlete->id,
								'name'					=> $activity->name,
								'distance'				=> $activity->distance,
								'moving_time'			=> $activity->moving_time,
								'elapsed_time'			=> $activity->elapsed_time,
								'start_date'			=> $activity->start_date,
								'start_date_local'		=> $activity->start_date_local,
								'location_city'			=> $activity->location_city,
								'location_state'		=> $activity->location_state,
								'location_country'		=> $activity->location_country,
								'gear_id'				=> $activity->gear_id,
								'average_speed'			=> $activity->average_speed,
								'max_speed'				=> $activity->max_speed,
								'type'					=> $activity->type
								
				);
				
				try {
					$request->user()->activities()->create($data);
				} catch(Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "<br>";
				}
				
		    
		    }
		    
		    $activity_count = count( $activities );
			$page++;
			
			echo( '<h2>Activity Count: ' . $activity_count . ' Page: ' . $page . '</h2>' );
		    
		}
		exit;
	}
	
}
