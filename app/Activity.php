<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
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
