<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersAdmin extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users', function (Blueprint $table) {
                $table->smallInteger('city_id')->nullable();
            }
        );

        Schema::create(
            'order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('product_id');
                $table->primary(['order_id', 'product_id']);
                $table->unsignedInteger('amount');
                $table->unsignedInteger('cost');
                $table->char('status', 1);
            }
        );

        Schema::drop('orders');

        Schema::create(
            'orders', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('manager_id')->nullable()->index();
                $table->string('code', 32);
                $table->string('status', 1)->default(\models\Orders::STATUS_NEW);
                $table->unsignedInteger('cost');
                $table->smallInteger('discount')->nullable();
                $table->string('user_name', 128);
                $table->string('email', 32)->nullable();
                $table->string('phone', 10);
                $table->smallInteger('city_id');
                $table->string('address')->nullable();
                $table->text('comments')->nullable();
                $table->text('note')->nullable();
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
        Schema::table(
            'users', function (Blueprint $table) {
                $table->dropColumn('city_id');
            }
        );

        Schema::drop('order_items');

        Schema::table(
            'orders', function (Blueprint $table) {
                $table->dropColumn('manager_id');
                $table->dropColumn('user_name');
                $table->dropColumn('email');
                $table->dropColumn('phone');
                $table->dropColumn('address');
                $table->dropColumn('cost');
                $table->dropColumn('discount');
                $table->dropColumn('note');
                $table->dropColumn('comments');
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
            }
        );
    }

}
