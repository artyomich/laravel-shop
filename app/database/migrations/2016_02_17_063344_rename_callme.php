<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCallme extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('callme', 'events');
        Schema::table(
            'events',
            function ($table) {
                $table->integer('user_id')->nullable();
                $table->date('begin')->nullable();
                $table->date('end')->nullable();
                $table->integer('type')->default(1);
            }
        );
        DB::statement("ALTER TABLE events ALTER COLUMN city_id DROP NOT NULL");
        DB::statement("ALTER TABLE events ALTER COLUMN phone DROP NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('events', 'callme');
        Schema::table(
            'callme', function ($table) {
            $table->dropColumn('user_id');
            $table->dropColumn('begin');
            $table->dropColumn('end');
            $table->dropColumn('type');
        });
        DB::statement("ALTER TABLE callme ALTER COLUMN city_id SET NOT NULL");
        DB::statement("ALTER TABLE callme ALTER COLUMN phone SET NOT NULL");

    }

}
