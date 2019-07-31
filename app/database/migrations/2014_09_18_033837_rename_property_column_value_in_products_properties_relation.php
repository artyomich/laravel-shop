<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePropertyColumnValueInProductsPropertiesRelation extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(\DB::raw("ALTER TABLE products_properties_relation RENAME COLUMN value TO property_value;"));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement(\DB::raw("ALTER TABLE products_properties_relation RENAME COLUMN property_value TO value;"));
    }

}
