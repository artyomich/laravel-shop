<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingCityIdToUsersContracts extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'users_contracts',
            function ($table) {
                $table->integer('city_id')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table(
            'users_contracts', function ($table) {
            $table->dropColumn('city_id');
        });
    }

}
