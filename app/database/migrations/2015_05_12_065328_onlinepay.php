<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Onlinepay extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::create(
			'transactions_gazprom_relation',
			function (Blueprint $table) {
				$table->bigInteger('transaction_id')->unique();
				$table->string('pps_id');
			}
		);

		\Schema::create(
			'transactions',
			function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->integer('order_id');
				$table->integer('cost');
				$table->string('comment')->nullable();
				$table->enum('status', ['new', 'success', 'fail']);
				$table->dateTime('date_create');
				$table->dateTime('date_update');
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
		Schema::drop('transactions');
		Schema::drop('transactions_gazprom_relation');
	}

}
