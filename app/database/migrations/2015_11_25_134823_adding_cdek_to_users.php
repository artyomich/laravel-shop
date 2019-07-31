<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingCdekToUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'users',
            function ($table) {
                $table->integer('cdek_id')->nullable();
                $table->string('city_name', 50)->nullable();
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
            'users', function ($table) {
            $table->dropColumn('cdek_id');
            $table->dropColumn('city_name');
        });
    }

}
