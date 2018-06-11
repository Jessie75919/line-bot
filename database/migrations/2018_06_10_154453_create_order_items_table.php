<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('訂單id');
            $table->unsignedInteger('product_id')->comment('商品id');
            $table->unsignedInteger('price')->comment('商品價格');
            $table->unsignedInteger('count')->comment('商品數量');
            $table->unsignedInteger('sub_total')->comment('小計');
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
        });
    }


    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
