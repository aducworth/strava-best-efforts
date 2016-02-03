<?php

namespace App;

use App\Activity;
use Illuminate\Database\Eloquent\Model;

class BestEffort extends Model
{
    /**
     * Get the activity that owns the best effort.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
