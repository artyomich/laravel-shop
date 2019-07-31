<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'users',
            /**
             * @var  $table
             */
            function ($table) {
                $table->string('phone', 15)->nullable();
                $table->string('firm', 40)->nullable();
                $table->boolean('is_firm', 15)->nullable();
                $table->string('address', 255)->nullable();
                $table->string('inn', 40)->nullable();
                $table->string('ogrn', 40)->nullable();
                $table->string('kpp', 40)->nullable();
                $table->string('rs', 40)->nullable();
                $table->string('ks', 40)->nullable();
                $table->string('bik', 40)->nullable();
                $table->string('bank', 255)->nullable();
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
            'users', function ($table) {
            $table->dropColumn('phone');
            $table->dropColumn('is_firm');
            $table->dropColumn('address');
            $table->dropColumn('inn');
            $table->dropColumn('ogrn');
            $table->dropColumn('kpp');
            $table->dropColumn('rs');
            $table->dropColumn('ks');
            $table->dropColumn('bik');
            $table->dropColumn('bank');
            $table->dropColumn('firm');
        }
        );
    }

}
