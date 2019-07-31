<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAdmin extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = Sentry::createUser(
            array(

                'email'       => 'user0851@ashk.ru',
                'password'    => 'pass',
                'activated'   => 1,
                'permissions' => array(
                    'superuser' => 1,
                ),
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::where('email', '=', 'user0851@ashk.ru')->firstOrFail()->delete();
    }

}
