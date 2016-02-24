<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/

/* User Authentication */



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    /* User Authentication */
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/logout', 'Auth\AuthController@logout');
	
	Route::get('auth/register', 'Auth\AuthController@getRegister');
	Route::post('auth/register', 'Auth\AuthController@postRegister');
	
	Route::get('/strava/_check_import', 'StravaController@checkImport');
	Route::get('/strava/_get_activities', 'StravaController@importActivities');
	Route::get('/strava/_get_best_efforts', 'StravaController@importBestEfforts');
	Route::get('/strava/activities', 'StravaController@activities');
	Route::get('/strava/import', 'StravaController@import');
	Route::get('/strava/stats', 'StravaController@stats');
	Route::get('/strava/authenticate', 'StravaController@authenticate');
	Route::get('/strava/connect', 'StravaController@connect');
	Route::get('/strava/profile', 'StravaController@profile');
	Route::get('/strava/running', 'StravaController@running');
	Route::get('/', 'StravaController@connect');
});
