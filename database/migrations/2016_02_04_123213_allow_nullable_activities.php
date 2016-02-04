<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
            $table->text('type')->nullable()->change();
            $table->text('location_city')->nullable()->change();
            $table->text('location_state')->nullable()->change();
            $table->text('location_country')->nullable()->change();
            $table->text('gear_id')->nullable()->change();
            $table->text('details')->nullable()->change();
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
            //
        });
    }
}
