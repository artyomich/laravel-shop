<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserEmailResize extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE orders ALTER COLUMN email TYPE CHARACTER VARYING(64)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE orders ALTER COLUMN email TYPE CHARACTER VARYING(32)');
    }

}
