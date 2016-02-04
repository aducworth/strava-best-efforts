<?php

namespace App;

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
}
