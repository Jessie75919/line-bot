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
            $table->string('name')->comment('店家名稱');
            $table->string('email')->unique()->comment('店家官方Email');
            $table->string('password');
            $table->string('phone',20)->comment('電話')->nullable();
            $table->string('address',100)->comment('地址')->nullable();
            $table->string('line_channel',100)->comment('LINE Channel ID')->nullable();
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
