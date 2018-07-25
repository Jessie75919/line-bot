<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->comment('商品id');;
            $table->string('file_name',40)->nullable()->comment('圖片檔名');;
            $table->string('category',10)->default('product')->comment('圖片類型');;
            $table->string('image_url',150)->nullable()->comment('圖片網址');;
            $table->boolean('status')->default('0')->comment('圖片上架狀態');;
            $table->integer('order')->comment('圖片順序');;
            $table->timestamps();

            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_images');
    }
}
