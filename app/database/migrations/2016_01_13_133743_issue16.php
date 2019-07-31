<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Issue16 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE users ALTER COLUMN firm TYPE CHARACTER VARYING(255)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE users ALTER COLUMN firm TYPE CHARACTER VARYING(40)');
    }

}
