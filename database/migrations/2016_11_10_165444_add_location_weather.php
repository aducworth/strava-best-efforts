<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationWeather extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->text('start_latlng')->after('location_city');
            $table->text('end_latlng')->after('start_latlng');
            $table->text('temperature')->after('end_latlng');
            $table->text('humidity')->after('temperature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('start_latlng');
            $table->dropColumn('end_latlng');
            $table->dropColumn('temperature');
            $table->dropColumn('humidity');
        });
    }
}
