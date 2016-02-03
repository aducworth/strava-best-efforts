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
	
	public function __construct() {
		
		$this->clientId = getenv('STRAVA_CLIENT_ID');
		$this->clientSecret = getenv('STRAVA_CLIENT_SECRET');
		
		$this->api = new Iamstuartwilson\StravaApi(
		    $this->clientId,
		    $this->clientSecret
		);
		
		$token = $this->api->setAccessToken('42fec3b2d5840a3400ac8412f000c15bc8deb697');
		
	}
	
	/**
	 * authenticate
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function authenticate(Request $request)
	{
		$activities = $this->api->authenticationUrl('http://local.strava/strava/_authenticate', 'auto', 'write', 'mystate');
		return response()->json($activities);
	}
	
    /**
	 * Get activities
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function activities(Request $request)
	{
		$activities = $this->api->get('athlete/activities', ['per_page' => 200,'page' => 1]);
		return response()->json($activities);
	}
}
