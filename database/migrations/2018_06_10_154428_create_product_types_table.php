<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30)->comment('產品類別名稱');
            $table->unsignedInteger('order')->nullable()->comment('類別排序順位');
            $table->unsignedInteger('is_launch')->nullable()->comment('是否上架');
            $table->unsignedInteger('shop_id')->comment('商家id');
            $table->timestamps();

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
        Schema::dropIfExists('product_types');
    }
}
