<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('profile_medium');
            $table->text('profile_large');
            $table->text('city');
            $table->text('state');
            $table->text('country');
            $table->text('sex');
            $table->boolean('premium');
            $table->text('date_preference');
            $table->text('measurement_preference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_medium');
            $table->dropColumn('profile_large');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('sex');
            $table->dropColumn('premium');
            $table->dropColumn('date_preference');
            $table->dropColumn('measurement_preference');
        });
    }
}
