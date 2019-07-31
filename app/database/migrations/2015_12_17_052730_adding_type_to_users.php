<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \models\Users;

class AddingTypeToUsers extends Migration
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
            function ($table) {
                $table->string('type', 50)->nullable();
            }
        );
        $users = Users::get();
        foreach ($users as $item) {
            Users::where('id', $item->id)->update(['type' => Users::getType($item)]);
        }
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
            $table->dropColumn('type');
        });
    }

}
