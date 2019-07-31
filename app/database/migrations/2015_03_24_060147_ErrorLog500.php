<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ErrorLog500 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(\DB::raw('ALTER TABLE log_errors ALTER COLUMN url TYPE text;'));
        \DB::statement(\DB::raw('ALTER TABLE log_errors ALTER COLUMN referer TYPE text;'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement(\DB::raw('ALTER TABLE log_errors ALTER COLUMN url TYPE character varying;'));
        \DB::statement(\DB::raw('ALTER TABLE log_errors ALTER COLUMN referer TYPE character varying;'));
    }

}
