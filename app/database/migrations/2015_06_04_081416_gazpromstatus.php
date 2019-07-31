<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Gazpromstatus extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
			'cities',
			function (Blueprint $table) {
				$table->boolean('enable_acquiring')->default('f');
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
			'cities',
			function (Blueprint $table) {
				$table->dropColumn('enable_acquiring');
			}
		);
	}

}
