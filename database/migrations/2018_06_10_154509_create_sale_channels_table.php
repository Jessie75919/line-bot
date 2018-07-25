<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',30)->comment('通路名稱');
            $table->unsignedInteger('shop_id')->comment('商品id');
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
        Schema::dropIfExists('sale_channels');
    }
}
