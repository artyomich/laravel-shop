<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLengthPhonesInCitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE cities ALTER COLUMN phones TYPE CHARACTER VARYING(32)');
        \DB::statement('ALTER TABLE cities ALTER COLUMN email TYPE CHARACTER VARYING(32)');
        \DB::statement('ALTER TABLE cities ALTER COLUMN address TYPE CHARACTER VARYING(64)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //  Нельзя откатиться назад
    }

}
