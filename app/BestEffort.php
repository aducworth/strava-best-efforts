<?php

namespace App;

use App\Activity;
use Illuminate\Database\Eloquent\Model;

class BestEffort extends Model
{
	
	protected $fillable = ['strava_id', 'activity_id', 'athlete_id','name','distance','moving_time','elapsed_time','start_date','start_date_local','kom_rank','pr_rank'];
	
    /**
     * Get the activity that owns the best effort.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
