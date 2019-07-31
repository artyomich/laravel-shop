<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingFieldEmployers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
			'cities',
			/**
			 * @var  $table
			 */
			function ($table) {
				$table->integer('default_manager')->nullable();
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
			'cities', function ($table) {
			$table->dropColumn('default_manager');
		}
		);
	}

}
