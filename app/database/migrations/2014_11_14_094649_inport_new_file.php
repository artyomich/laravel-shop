<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InportNewFile extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(\DB::raw('ALTER TABLE products_properties ALTER COLUMN brand TYPE character varying(128);'));
        \DB::statement(
            \DB::raw('ALTER TABLE products_properties ALTER COLUMN manufacturer TYPE character varying(128);')
        );
        \DB::statement(\DB::raw('ALTER TABLE products_properties ALTER COLUMN model TYPE character varying(128);'));
        \DB::statement(\DB::raw('ALTER TABLE products_properties ALTER COLUMN size TYPE character varying(128);'));
        \DB::statement(\DB::raw('ALTER TABLE products_properties ALTER COLUMN series TYPE character varying(128);'));
        \DB::statement(\DB::raw('ALTER TABLE products_properties ALTER COLUMN season TYPE character varying(128);'));
        \DB::statement(
            \DB::raw('ALTER TABLE products_properties ALTER COLUMN image_axis TYPE character varying(128);')
        );
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
