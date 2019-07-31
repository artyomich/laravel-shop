<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BigCitiesPhoneNumber extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(\DB::raw('ALTER TABLE cities ALTER COLUMN phones TYPE character varying(64);'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement(\DB::raw('ALTER TABLE cities ALTER COLUMN phones TYPE character varying(32);'));
    }

}
