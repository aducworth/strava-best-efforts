<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('strava_id');
            $table->integer('athlete_id');
            $table->text('name');
            $table->text('type');
            $table->integer('distance');
            $table->integer('moving_time');
            $table->integer('elapsed_time');
            $table->dateTime('start_date');
            $table->dateTime('start_date_local');
            $table->text('location_city');
            $table->text('location_state');
            $table->text('location_country');
            $table->text('gear_id');
            $table->integer('average_speed');
            $table->integer('max_speed');
            $table->integer('calories');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('activities');
    }
}
