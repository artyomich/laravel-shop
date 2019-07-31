<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingId1cToUsersContracts extends Migration {

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
				$table->string('id_1c', 50)->nullable();
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
			$table->dropColumn('id_1c');
		});
	}

}
