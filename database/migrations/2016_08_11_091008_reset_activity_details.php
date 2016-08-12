<?php

use App\Activity;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResetActivityDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $activities = Activity::where('type', 'Run')->orderBy('start_date_local', 'desc')->get();
        
        foreach( $activities as $activity ) {
	        
	        $activity->details = 0;
	        
	        $activity->save();
	        
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
