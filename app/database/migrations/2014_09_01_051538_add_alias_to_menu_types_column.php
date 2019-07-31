<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAliasToMenuTypesColumn extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'menus_types', function (Blueprint $table) {
                $table->string('alias')->length(16)->nullable();
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
        Schema::table(
            'menus_types', function (Blueprint $table) {
                $table->dropColumn('alias');
            }
        );
    }

}
