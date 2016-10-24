<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Activity;
use App\BestEffort;
use App\Split;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Iamstuartwilson;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Mail;

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
			
			if( isset( $token->athlete ) ) {
			
				// changing to use email instead of access token since access token apparently changes
				$user = User::firstOrCreate(['email' => $token->athlete->email]);
				
				if( !$user->strava_token ) {
					
					Mail::send('emails.newuser', ['name' => ( $token->athlete->firstname . " " . $token->athlete->lastname )], function($message)
					{
						$message->from('admin@stravabestefforts.com', 'Strava BE');
					    $message->to(env('MANDRILL_EMAIL'), 'Austin Ducworth')->subject('New Strava BE User');
					});
				
				}
				
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
		            return redirect()->intended('strava/running');
		        }
	        
	        }
		}
		
	}
	
	/**
	 * Send a support request.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function support(Request $request)
	{
		if( $request->message ) {
			
			Mail::send('emails.support', ['email' => Auth::user()->email, 'supportMessage' => $request->message, 'url' => $request->url], function($supportEmail)
			{
				$supportEmail->from('admin@stravabestefforts.com', 'Strava BE');
			    $supportEmail->to(env('MANDRILL_EMAIL'), 'Austin Ducworth')->subject('Strava BE Support Request');
			});
			
			return response()->json(['result' => true]);
			
		}
		
		return response()->json(['result' => false, 'errors' => 'Please include a message.']);
	}
	
	/**
	 * Show the support form.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function supportForm(Request $request)
	{
		return view('strava.supportForm');
		
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
    ->selectRaw('be.elapsed_time, be.moving_time, be.start_date_local, activities.name, activities.distance, activities.strava_id, be.distance as effort_distance')->orderBy('be.elapsed_time', 'asc');
    	
    	// make sure a distance is selected
    	if( $request->distance ) {
	    	
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
	    	
    	} else {
	    	
	    	$efforts = [];
	    	
    	}
    	
	
	    return view('strava.running', [
	        'efforts' => $efforts,
	        'distances' => $distances
	    ]);
	}
	
	/**
	 * Analyze splits.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function splits(Request $request)
	{
				
	    $query = $request->user()->activities()->where('type','Run')->orderBy('start_date_local', 'desc');
	    
	    $split_count = $request->split_count?$request->split_count:1;
    	
    	// make sure a distance is selected
    	if( $request->from_date || $request->to_date ) {
	    		    	
	    	if( $request->from_date ) {
				$query->where('start_date_local','>=',date('Y-m-d',strtotime($request->from_date)));
			}
			
			if( $request->to_date ) {
				$query->where('start_date_local','<=',date('Y-m-d',strtotime('+1 day',strtotime($request->to_date))));
			}
	    	
	    	$runs = $query->get();
	    	
    	} else {
	    	
	    	$runs = array();
	    	
    	}
    	
	
	    return view('strava.splits', [
	        'runs' => $runs,
	        'split_count' => $split_count
	    ]);
	}
	
	/**
	 * Take action on multiple activities.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function multi(Request $request)
	{
		
		if( $request->multi_edit ) {
			
			foreach( $request->multi_edit as $activity_id ) {
				
				$activity = Activity::where('strava_id', $activity_id)->first();
				
				if( $activity ) {
					
					$activity->bestEfforts()->delete();
					
					$activity->splits()->delete();
					
					$activity->delete();
									
				}
				
			}
			
		}
		
		return redirect()->intended('strava/activities?type='.$request->type.'&from_date='.$request->from_date.'&to_date='.$request->to_date);
		
	}
	
	/**
	 * Display a list of all of the user's activities.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function activities(Request $request)
	{
		
		$types = Activity::where('user_id', $request->user()->id)->orderBy('type','asc')->groupBy('type')->lists('type','type');
		
	    $query = Activity::where('user_id', $request->user()->id)->orderBy('start_date_local', 'desc');
	    
    	if( $request->type ) {
	    	$query->where('type',$request->type);
    	}
    	
    	if( $request->from_date ) {
			$query->where('start_date_local','>=',date('Y-m-d',strtotime($request->from_date)));
		}
		
		if( $request->to_date ) {
			$query->where('start_date_local','<=',date('Y-m-d',strtotime('+1 day',strtotime($request->to_date))));
		}
	    	
	    	$activities = $query->get();
	
	    return view('strava.activities', [
	        'activities' => $activities, 'types' => $types
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
				
		if( $this->importActivities() ) {
			
			return $this->importDetails();
			
		}
		
		$response = new StreamedResponse(function () {
			echo ( 'data: ' . json_encode(['message' => 'Rate Limit Exceeded']) . "\n\n");
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        return $response;

	}
	
	/**
	 * Check to see if we imported today.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function checkImport(Request $request)
	{	
	    $user = Auth::user();
		
		$import_date = $user->import_date;
		$today = date('Y-m-d');
		
		if( strtotime( $import_date ) < strtotime( $today ) ) {
			return response()->json(['result' => true]);
		}
		
		return response()->json(['result' => false]);
	}
	
	/**
	 * Show information regarding yearly running goal.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function goals(Request $request)
	{
		
		if( isset($request->goal) ) {
			
			$request->user()->yearly_running_goal = $request->goal;
			$request->user()->save();
			
		}
		
		$goal = $request->user()->yearly_running_goal;
		
		// do the standard metric goal conversion
		if( Auth::user()->measurement_preference == 'feet' ) {
			
			$goal = $goal * 1609; // convert to miles
			
		} else {
			
			$goal = $goal * 1000;
			
		}
		
		$last_year 		= date('Y-12-31',strtotime('-1 year'));
		$miles_to_date 	= 0;
		$time_to_date	= 0;
		
		$week_of_year = date('W');
		$weeks_left		= ( 52 - $week_of_year );
		
		$runs = Activity::where('user_id', $request->user()->id)->where('start_date_local','>',$last_year)->where('type','Run')->orderBy('start_date_local', 'asc')->get();
		
		foreach( $runs as $run ) {
			
			$miles_to_date += $run->distance;
			$time_to_date += $run->moving_time;
			
		}
		
		// check to see if the goal has been met
		if( $miles_to_date >= $goal ) {
			
			$miles_to_go = Activity::formatDistance( 0 );
			$weekly_goal = Activity::formatDistance( 0 );
			
		} else {
			
			$miles_to_go = Activity::formatDistance( $goal - $miles_to_date );
			$weekly_goal = Activity::formatDistance( ( $goal - $miles_to_date ) / $weeks_left );
			
		}
		
		return view('strava.goals',[ 'yearly_running_goal' => $request->user()->yearly_running_goal, 'week_of_year' => $week_of_year, 'miles_to_date' => Activity::formatDistance(  $miles_to_date ), 'time_to_date' => Activity::formatTime( $time_to_date ), 'weekly_mileage' => Activity::formatDistance( $miles_to_date / $week_of_year ), 'weeks_left' => $weeks_left, 'miles_to_go' => $miles_to_go, 'weekly_goal' => $weekly_goal ]);
		
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
		
		$monthly_totals = [];
		
		$month_array = [];
		$distance_array = [];
		
		$month_string = "";
		$distance_strings = [];
		
		//$distances = array( '400m', '1/2 mile', '1 mile', '2 mile', '5k', '10k', '15k', '10 mile', '20k' );
		$distances = array( '400m', '1/2 mile' );
		
		foreach( $activities as $activity ) {
			
			if( !isset( $monthly_totals[date('m/Y',strtotime($activity->start_date_local))] ) ) {
				$monthly_totals[date('m/Y',strtotime($activity->start_date_local))] = [];
			}
			if( !isset( $monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type] ) ) {
				$monthly_totals[date('m/Y',strtotime($activity->start_date_local))][$activity->type] = [ 'distance' => 0, 'time' => 0 ];
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
					$distance_array[$distance] = [];
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
	public function importActivities()
	{
		$page 				= 1;
		$activity_count 	= 200;
		$imported 			= 0;
		$finished			= false;
		
		while( $activity_count == 200 && $page < 10 ) {
		
			$activities = $this->api->get('athlete/activities', ['per_page' => 200,'page' => $page]);
			
			if(isset($activities->errors) && count($activities->errors)) {
				
				return false;
				
			}
			
			foreach( $activities as $activity ) {
				
				try {
					
					$activity_update = Auth::user()->activities()->firstOrCreate(['strava_id' => $activity->id]);
					
					// check to see if this is new
					if( !$activity_update->name ) {
						$imported++;
					} else {
						// adding break once we get to activities already imported
						$finished = true;
						break;
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
		    
		    // break out and finish import if we find previously imported entries
		    if( $finished ) {
			    break;
		    }
		    
		    $activity_count = count( $activities );
			$page++;
			
		}
		
		// save the date and last import page
		$user = Auth::user();
		
		$user->import_page = ($page-1);
		$user->import_date = date('Y-m-d');
		
		$user->save();
		
		return true;
		
	}
	
	/**
	 * Import details
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function importDetails()
	{
		
		
		$response = new StreamedResponse(function () {
			
			$runs = Auth::user()->activities()->where('details',0)->where('type','Run')->get();
			$imported = 0;
			$runs_imported = 0;

			if( count($runs) ) {
				
				ob_start();
	            foreach( $runs as $run ) {
				
					$strava_run = $this->api->get('activities/' . $run->strava_id);
					
					if(isset($strava_run->errors) && count($strava_run->errors)) {
				
						echo ( 'data: ' . json_encode(['message' => $strava_run->message]) . "\n\n");
						ob_flush();
			            flush();
			            break;
						
					}
				
					if( isset( $strava_run->best_efforts ) && count( $strava_run->best_efforts ) > 0 ) {
										
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
					
					if( isset( $strava_run->splits_standard ) && count( $strava_run->splits_standard ) > 0 ) {
										
						foreach( $strava_run->splits_standard as $ss ) {
							
							$split = $run->splits()->firstOrCreate(['activity_id' => $strava_run->id,'type' => 'standard','split'=>$ss->split]);		
							
							// check to see if this is new
							if( !$split->distance ) {
								$imported++;
							}			
							
							$split->split				= $ss->split;
							$split->distance			= $ss->distance;
							$split->moving_time			= $ss->moving_time;
							$split->elapsed_time		= $ss->elapsed_time;
							$split->elevation_difference	= $ss->elevation_difference;
							
							$split->save();
							
						}
						
					}
					
					if( isset( $strava_run->splits_metric ) && count( $strava_run->splits_metric ) > 0 ) {
										
						foreach( $strava_run->splits_metric as $ss ) {
							
							$split = $run->splits()->firstOrCreate(['activity_id' => $strava_run->id,'type' => 'metric','split'=>$ss->split]);
							
							// check to see if this is new
							if( !$split->distance ) {
								$imported++;
							}			
							
							$split->split				= $ss->split;
							$split->distance			= $ss->distance;
							$split->moving_time			= $ss->moving_time;
							$split->elapsed_time		= $ss->elapsed_time;
							$split->elevation_difference	= $ss->elevation_difference;
							
							$split->save();
							
						}
						
					}
					
					$runs_imported++;
					
					$run->details = 1;
					$run->save();
					
					$percentage = number_format( ($runs_imported/count($runs)) * 100 );
					
					echo ( 'data: ' . json_encode(['refresh' => $percentage]) . "\n\n");
					ob_flush();
		            flush();
				
				}
				
				
			} else {
				ob_start();
				echo ( 'data: ' . json_encode(['refresh' => 100]) . "\n\n");
				ob_flush();
		        flush();
			}
            

        });

        $response->headers->set('Content-Type', 'text/event-stream');
        return $response;
	
	}
	
}
