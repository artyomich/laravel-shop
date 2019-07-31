<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixKeywordsLengthInProductsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE products ALTER COLUMN meta_title TYPE CHARACTER VARYING(255)');
        \DB::statement('ALTER TABLE products ALTER COLUMN meta_keywords TYPE text');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //\DB::statement('DELETE FROM products');
        //\DB::statement('ALTER TABLE products ALTER COLUMN meta_title TYPE CHARACTER VARYING(128)');
        //\DB::statement('ALTER TABLE products ALTER COLUMN meta_keywords TYPE CHARACTER VARYING(128)');
    }

}
