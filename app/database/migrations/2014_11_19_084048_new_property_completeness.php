<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewPropertyCompleteness extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products_properties', function (Blueprint $table) {
                $table->string('completeness')->nullable();
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
            'products_properties', function (Blueprint $table) {
                $table->dropColumn('completeness');
            }
        );
    }

}
