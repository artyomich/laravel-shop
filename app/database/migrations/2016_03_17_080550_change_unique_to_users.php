<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueToUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'users', function (Blueprint $table) {
            $table->unique(['inn', 'kpp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table(
            'users', function (Blueprint $table) {
            $table->dropUnique('users_inn_kpp_unique');
        });
    }

}
