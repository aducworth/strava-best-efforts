<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Activity;
use App\BestEffort;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Iamstuartwilson;

class StravaController extends Controller
{
	var $clientId;
	var $clientSecret;
	var $api;
	
	public function __construct(Request $request = null) {
		
		$this->middleware('auth', ['except' => array('connect', 'authenticate')]);
		
		$this->clientId = getenv('STRAVA_CLIENT_ID');
		$this->clientSecret = getenv('STRAVA_CLIENT_SECRET');
		
		$this->api = new Iamstuartwilson\StravaApi(
		    $this->clientId,
		    $this->clientSecret
		);
		
		if( isset( $request->user()->strava_token ) && $request->user()->strava_token != "" ) {
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
	    $url = $this->api->authenticationUrl('http://' . $_SERVER['SERVER_NAME'] . '/strava/authenticate', 'auto', 'write', 'mystate');
	
	    return view('strava.connect', [
	        'url' => $url,
	    ]);
	}
	
	/**
	 * Display profile page
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function profile(Request $request)
	{
	    return view('strava.profile');
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
		
		if( $code ) {
			$token = $this->api->tokenExchange($code);
			
			$user = User::firstOrCreate(['strava_token' => $token->access_token]);
			
			// fill in data
			$user->strava_token = $token->access_token;
			$user->name = ( $token->athlete->firstname . " " . $token->athlete->lastname );
			$user->profile_medium = $token->athlete->profile_medium;
			$user->city = $token->athlete->city;
			$user->state = $token->athlete->state;
			$user->country = $token->athlete->country;
			$user->sex = $token->athlete->sex;
			$user->premium = $token->athlete->premium;
			$user->date_preference = $token->athlete->date_preference;
			$user->measurement_preference = $token->athlete->measurement_preference;
			$user->email = $token->athlete->email;
			$user->password = bcrypt('stravapassword');
			
			$user->save();
			
			if (Auth::attempt(['email' => $token->athlete->email, 'password' => 'stravapassword']))
	        {
	            return redirect()->intended('strava/import');
	        }
		}
		
	}
	
	/**
	 * Display a list of all of the user's best efforts.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function running(Request $request)
	{
		
		$distances = BestEffort::orderBy('distance','asc')->groupBy('name')->lists('name','name');
		
	    $query = $request->user()->activities()->join('best_efforts as be', 'be.activity_id', '=', 'activities.id')
    ->selectRaw('be.elapsed_time, be.moving_time, be.start_date_local')->orderBy('be.moving_time', 'asc');
    	
    	if( $request->distance ) {
	    	$query->where('be.name',$request->distance);
    	}
    	
    	if( $request->from_date ) {
			$query->where('be.start_date_local','>=',date('Y-m-d',strtotime($request->from_date)));
		}
		
		if( $request->to_date ) {
			$query->where('be.start_date_local','<=',date('Y-m-d',strtotime('+1 day',strtotime($request->to_date))));
		}
    	
    	$efforts = $query->get();
	
	    return view('strava.running', [
	        'efforts' => $efforts,
	        'distances' => $distances
	    ]);
	}
	
	/**
	 * Display a list of all of the user's activities.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function activities(Request $request)
	{
	    $activities = Activity::where('user_id', $request->user()->id)->orderBy('start_date_local', 'desc')->get();
	
	    return view('strava.activities', [
	        'activities' => $activities,
	    ]);
	}
	
	/**
	 * Import activities showing results.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function import(Request $request)
	{	
	    return view('strava.import');
	}
	
	/**
	 * Show stats from best efforts.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function stats(Request $request)
	{
		
		$activities = Activity::where('user_id', $request->user()->id)->where('start_date_local','>','2015-01-01')->orderBy('start_date_local', 'asc')->get();
		
		$monthly_totals = array();
		
		$month_array = array();
		$distance_array = array();
		
		$month_string = "";
		$distance_strings = array();
		
		//$distances = array( '400m', '1/2 mile', '1 mile', '2 mile', '5k', '10k', '15k', '10 mile', '20k' );
		$distances = array( '400m', '1/2 mile' );
		
		foreach( $activities as $activity ) {
			
			if( !isset( $monthly_totals[date('m/Y',strtotime($activity->start_date_local))] ) ) {
				$monthly_totals[date('m/Y',strtotime($activity->start_date_local))] = array();
			}
			if( !isset( $monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type] ) ) {
				$monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type] = array( 'distance' => 0, 'time' => 0 );
			}
			
			$monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type]['distance'] += ( $activity->distance * 0.000621371 );
			$monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type]['time'] += $activity->moving_time;
			
			$best_efforts = $activity->bestEfforts()->orderBy('start_date_local', 'desc')->get();
			
			if( count( $best_efforts ) ) {
				
				foreach( $best_efforts as $best_effort ) {
					
					if( !isset( $monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type][$best_effort->name] ) ) {
						$monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type][$best_effort->name] = $best_effort->moving_time;
					}
				
					if( $best_effort->moving_time < $monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type][$best_effort->name] ) {
						$monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type][$best_effort->name] = $best_effort->moving_time;
					}
					
				}
			}
			
		}
		
		foreach( $monthly_totals as $month => $totals ) {
			
			$month_array[] = ("'".$month."'");
			
			foreach( $distances as $distance ) {
				
				if( !isset($distance_array[$distance]) ) {
					$distance_array[$distance] = array();
				}
				
				if( isset($totals['Run'][$distance]) ) {
					$distance_array[$distance][] = round( $totals['Run'][$distance], 2 );
				} else {
					$distance_array[$distance][] = 0;
				}
			}
			
		}
		
		foreach( $distance_array as $distance => $results ) {
			$distance_strings[$distance] = implode(",",$results);
		}
		
	    return view('strava.stats',['months' => implode(",",$month_array),'distances' => $distance_strings]);
	}
	
    /**
	 * Import activities
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function importActivities(Request $request)
	{
		$page 			= 1;
		$activity_count = 200;
		$imported = 0;
		
		while( $activity_count == 200 && $page < 10 ) {
		
			$activities = $this->api->get('athlete/activities', ['per_page' => 200,'page' => $page]);
			
			foreach( $activities as $activity ) {
								
				try {
					
					$activity_update = $request->user()->activities()->firstOrCreate(['strava_id' => $activity->id]);
					
					// check to see if this is new
					if( !$activity_update->name ) {
						$imported++;
					}
							 
					$activity_update->strava_id				= $activity->id;
					$activity_update->athlete_id 			= $activity->athlete->id;
					$activity_update->name					= $activity->name;
					$activity_update->distance				= $activity->distance;
					$activity_update->moving_time			= $activity->moving_time;
					$activity_update->elapsed_time			= $activity->elapsed_time;
					$activity_update->start_date			= $activity->start_date;
					$activity_update->start_date_local		= $activity->start_date_local;
					$activity_update->location_city			= $activity->location_city;
					$activity_update->location_state		= $activity->location_state;
					$activity_update->location_country		= $activity->location_country;
					$activity_update->gear_id				= $activity->gear_id;
					$activity_update->average_speed			= $activity->average_speed;
					$activity_update->max_speed				= $activity->max_speed;
					$activity_update->type					= $activity->type;
							 
					$activity_update->save();
					
				} catch(Exception $e) {
					echo 'Caught exception: ',  $e->getMessage(), "<br>";
				}				
		    
		    }
		    
		    $activity_count = count( $activities );
			$page++;
			
		}
		
		return response()->json(['imported' => $imported]);
		
	}
	
	/**
	 * Import best efforts
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function importBestEfforts(Request $request)
	{
		$runs = $request->user()->activities()->where('details',0)->where('type','Run')->get();
		$imported = 0;
	
		foreach( $runs as $run ) {
			
			$strava_run = $this->api->get('activities/' . $run->strava_id);
		
			if( count( $strava_run->best_efforts ) > 0 ) {
								
				foreach( $strava_run->best_efforts as $be ) {
					
					$best_effort = $run->bestEfforts()->firstOrCreate(['strava_id' => $be->id]);		
					
					// check to see if this is new
					if( !$best_effort->name ) {
						$imported++;
					}			
					
					$best_effort->strava_id				= $be->id;
					$best_effort->athlete_id 			= $be->athlete->id;
					$best_effort->name					= $be->name;
					$best_effort->distance				= $be->distance;
					$best_effort->moving_time			= $be->moving_time;
					$best_effort->elapsed_time			= $be->elapsed_time;
					$best_effort->start_date			= $be->start_date;
					$best_effort->start_date_local		= $be->start_date_local;
					$best_effort->pr_rank				= $be->pr_rank;
					
					$best_effort->save();
					
				}
				
			}
			
			$run->details = 1;
			$run->save();
			
		}
		
		return response()->json(['imported' => $imported]);
	
	}
	
}
