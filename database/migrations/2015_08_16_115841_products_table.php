<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("category_id");
            $table->integer("subcategory_id");
            $table->integer("price_id");
            $table->string('product_name');
            $table->text('description');
            $table->datetime("date");
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('products');
    }
}
