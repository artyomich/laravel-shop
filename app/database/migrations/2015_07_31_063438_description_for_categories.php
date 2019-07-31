<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DescriptionForCategories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        \Schema::table(
                'categories', function($table) {
            $table->text('description')->nullable();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table(
                'categories', function ($table) {
            $table->dropColumn('description');
        }
        );
    }

}
