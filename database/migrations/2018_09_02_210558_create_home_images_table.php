<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_id')->comment('商家id');
            $table->string('name',50)->comment('主圖標題');
            $table->unsignedInteger('order')->default('0')->comment('排序');
            $table->boolean('is_launch')->comment('是否上架');
            $table->string('file_name')->comment('圖檔名稱');
            $table->string('image_url',255)->comment('圖檔位址');
            $table->string('link',255)->comment('主圖連結');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('home_images');
    }
}
