<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductOnIndexPageColumn extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products', function ($table) {
                $table->boolean('is_on_index')->default('FALSE');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table(
            'products', function ($table) {
                $table->dropColumn('is_on_index');
            }
        );
    }

}
