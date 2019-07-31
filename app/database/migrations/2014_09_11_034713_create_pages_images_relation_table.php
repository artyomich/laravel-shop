<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesImagesRelationTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'pages_images_relation', function (Blueprint $table) {
                $table->bigInteger('page_id');
                $table->bigInteger('image_id');
                $table->index(['page_id', 'image_id']);
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
        Schema::drop('pages_images_relation');
    }

}
