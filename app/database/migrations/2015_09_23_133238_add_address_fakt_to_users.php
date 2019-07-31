<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressFaktToUsers extends Migration
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
                $table->string('actual_address', 64)->nullable();
                $table->string('id_1c', 50)->nullable();
                $table->boolean('access')->nullable();
                $table->unique('inn');
                $table->unique('kpp');
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
            $table->dropColumn('actual_address');
            $table->dropColumn('id_1c');
            $table->dropColumn('access');
            $table->dropUnique('users_inn_unique');
            $table->dropUnique('users_kpp_unique');
        });
    }

}
