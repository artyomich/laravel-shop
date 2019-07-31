<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixMetaTitleColumnTo128Length extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(\DB::raw("ALTER TABLE pages ALTER COLUMN meta_title TYPE character varying(128);"));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(\DB::raw("ALTER TABLE pages ALTER COLUMN meta_title TYPE character varying(64);"));
    }

}
