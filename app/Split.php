<?php

namespace App;

use App\Activity;
use Illuminate\Database\Eloquent\Model;

class Split extends Model
{
	
	protected $fillable = ['activity_id', 'type', 'split', 'distance','moving_time','elapsed_time','elevation_gain'];
	
    /**
     * Get the activity that owns the best effort.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
