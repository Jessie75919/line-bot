<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSubTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sub_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('產品子類別名稱');
            $table->unsignedInteger('shop_id')->comment('商家id');
            $table->unsignedInteger('product_type_id')->comment('產品類別id');
            $table->unsignedInteger('order')->nullable()->comment('類別排序順位');
            $table->timestamps();

            $table->index('product_type_id');
            $table->index('shop_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sub_types');
    }
}
