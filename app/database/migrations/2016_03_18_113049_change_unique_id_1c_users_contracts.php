<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueId1cUsersContracts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
			'users_contracts', function (Blueprint $table) {
			$table->unique('id_1c');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Schema::table(
			'users_contracts', function (Blueprint $table) {
			$table->dropUnique('users_contracts_id_1c_unique');
		});
	}

}
