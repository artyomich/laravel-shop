<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLengthOfMenusNameColumn extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE menus ALTER COLUMN name TYPE CHARACTER VARYING(64)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE menus ALTER COLUMN name TYPE CHARACTER VARYING(16)');
    }

}
