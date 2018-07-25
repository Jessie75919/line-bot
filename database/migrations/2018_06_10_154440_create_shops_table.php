<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shop_sn',50)->comment('店家編號')->unique();
            $table->string('name',20)->comment('店家名稱');
            $table->string('email',50)->unique()->comment('店家官方Email');
            $table->string('password');
            $table->string('phone',20)->comment('電話')->nullable();
            $table->string('address',100)->comment('地址')->nullable();
            $table->string('line_channel',100)->comment('LINE Channel ID')->nullable();

            $table->index('shop_sn');

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
        Schema::dropIfExists('shops');
    }
}
