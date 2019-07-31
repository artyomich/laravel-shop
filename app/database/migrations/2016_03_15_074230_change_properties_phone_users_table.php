<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePropertiesPhoneUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'users', function (Blueprint $table) {
            $table->dropUnique('users_inn_unique');
            $table->dropUnique('users_kpp_unique');
        });
        \DB::statement('ALTER TABLE users ALTER COLUMN phone TYPE CHARACTER VARYING(255)');
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
            $table->unique('inn');
            $table->unique('kpp');
        });
        \DB::statement('ALTER TABLE users ALTER COLUMN phone TYPE CHARACTER VARYING(15)');
    }

}
