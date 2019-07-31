<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingTimestampsToCallme extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
				'callme',
				function ($table) {
					$table->nullableTimestamps();
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
				'callme', function ($table) {
			$table->dropTimestamps();
		});
	}

}
