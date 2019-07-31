<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClearImageProducts extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $errs = [];
        $exists = [];

        $products = \models\Products::all();

        foreach ($products as $product) {
            if (count($product->images)) {
                $_filename = explode(':', $product->images[0]->filename);
                $exists[] = end($_filename);
            }
        }

        $files = glob(public_path() . '/files/images/products/originals/*');
        foreach ($files as $fullName) {
            $file = basename($fullName);
            if (!in_array($file, $exists) && is_file($fullName)) {
                unlink($fullName);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
