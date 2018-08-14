<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
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
            $table->unsignedInteger('product_type_id')->comment('產品類別id');
            $table->unsignedInteger('product_sub_type_id')->nullable()->comment('產品子類別id');
            $table->unsignedInteger('shop_id')->comment('商家id');
            $table->string('name',25)->comment('商品名稱');
            $table->unsignedInteger('price')->comment('商品價格');
            $table->unsignedInteger('order')->nullable()->comment('商品排序順位');
            $table->mediumText('description')->nullable()->comment('商品敘述');
            $table->boolean('is_launch')->default('0')->comment('是否上架');
            $table->boolean('is_sold_out')->default('0')->comment('是否完售');
            $table->boolean('is_hottest')->default('0')->comment('是否為熱門商品');
            $table->timestamps();

            $table->index('product_type_id');
            $table->index('product_sub_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
