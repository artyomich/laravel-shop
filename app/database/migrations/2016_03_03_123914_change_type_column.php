<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeColumn extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'users_contracts',
            function ($table) {
                $table->dropColumn('cost_type');
            }
        );
        \Schema::table(
            'users_contracts',
            function ($table) {
                $table->string('cost_type', 20)->nullable();
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
            'users_contracts', function ($table) {
            $table->dropColumn('cost_type');
        });
        \Schema::table(
            'users_contracts', function ($table) {
            $table->integer('cost_type')->nullable();
        });
    }

}
