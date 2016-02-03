<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBestEffortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('best_efforts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('strava_id');
            $table->integer('activity_id');
            $table->integer('athlete_id');
            $table->text('name');
            $table->decimal('distance');
            $table->integer('kom_rank');
            $table->integer('pr_rank');
            $table->integer('elapsed_time');
            $table->integer('moving_time');
            $table->dateTime('start_date');
            $table->dateTime('start_date_local');
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
        Schema::drop('best_efforts');
    }
}
