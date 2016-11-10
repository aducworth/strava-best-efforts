<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
	
	protected $fillable = ['strava_id', 'athlete_id', 'name','type','distance','moving_time','elapsed_time','start_date','start_date_local','location_city','location_state','location_country','gear_id','average_speed','max_speed','calories','details'];
	
    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get all of the best efforts for the activity.
     */
    public function bestEfforts()
    {
        return $this->hasMany(BestEffort::class);
    }
    
     /**
     * Get all of the splits for the activity.
     */
    public function splits()
    {
        return $this->hasMany(Split::class);
    }
    
    /**
     * Format distances in readable format.
     */
    public static function formatDistance($distance) {
	    
	    if( Auth::user()->measurement_preference == 'feet' ) {
		    
		    return number_format( $distance * 0.000621371, 2 ) . ' mi';
		    
	    }
	    
	    if( $distance < 1000 ) {
		    
		    return $distance . 'm';
		    
	    }
	    
	    return number_format( $distance / 1000, 1 ) . ' km'; 
	    
    }
    
    /**
     * Format times in readable format.
     */
    public static function formatTime($time) {
	    
	    $minutes = floor( $time / 60 );
	    $seconds = ( $time % 60 );
	    $hours = floor( $minutes / 60 );
	    $minutes = ( $minutes % 60 );
	    
	    return ($hours?($hours.':'):'') . ($minutes?(sprintf("%02d", $minutes).':'):'00:') . ($seconds?(sprintf("%02d", $seconds).''):'00'); 
	    
    }
    
    /**
     * Format dates in readable format.
     */
    public static function formatDate($date) {
	    
	    return date('m/d/Y g:ia', strtotime( $date )); 
	    
    }
    
    /**
     * Calculate pace.
     */
    public static function calculatePace($meters,$seconds) {
	    
	    // if doing pace / mile
	    if( Auth::user()->measurement_preference == 'feet' ) {
		    
		    $mile 				= 1609;
		    $mile_distance		= $meters / $mile;
		    $pace				= ( $mile_distance > 0 )?( $seconds / $mile_distance ):0;
		    
		    return ( static::formatTime($pace) . ' / mi' );
		    
	    }
	    
	    $kilometer 			= 1000;
	    $km_distance		= $meters / $kilometer;
	    $pace				= ( $km_distance > 0 )?( $seconds / $km_distance ):0;
	    
	    return ( static::formatTime($pace) . ' / km' );
	    
    } 
    
    /**
     * Format temp.
     */
    public static function formatTemp($temp) {
	    
	    if( $temp == '' ) {
		    
		    return '';
		    
	    }
	    
	    // if doing pace / mile
	    if( Auth::user()->measurement_preference == 'feet' ) {
		    		    
		    return ( round( ( $temp * (9/5) ) - 459.67 ) . '&deg;F' );
		    
	    }
	    
	    return ( round( $temp - 273.15 ) . '&deg;C' );
	    
    } 
}
