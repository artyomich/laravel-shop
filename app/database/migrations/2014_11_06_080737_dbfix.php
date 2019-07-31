<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dbfix extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(\DB::raw('ALTER TABLE pages ALTER COLUMN meta_title TYPE character varying(255);'));
        \DB::statement(\DB::raw('ALTER TABLE pages ALTER COLUMN meta_keywords TYPE character varying(255);'));
        \DB::statement(\DB::raw('ALTER TABLE products_properties ALTER COLUMN model TYPE character varying(64);'));

        \DB::statement(\DB::raw('ALTER TABLE properties ALTER COLUMN name TYPE character varying(64);'));

        \DB::statement(\DB::raw('ALTER TABLE menus_types ALTER COLUMN name TYPE character varying(32);'));
        \DB::statement(\DB::raw('ALTER TABLE menus_types ALTER COLUMN alias TYPE character varying(32);'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
