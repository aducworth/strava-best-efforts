<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Splits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('splits', function (Blueprint $table) {
            $table->increments('id');
            $table->text('type');
            $table->integer('split');
            $table->integer('distance');
            $table->integer('moving_time');
            $table->integer('elapsed_time'); 
            $table->integer('elevation_difference');            
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
        Schema::table('splits', function (Blueprint $table) {
            Schema::drop('splits');
        });
    }
}
