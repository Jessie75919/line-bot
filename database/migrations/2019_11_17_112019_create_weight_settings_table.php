<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeightSettingsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('weight_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('memory_id');
            $table->float('height')->nullable();
            $table->float('goal_weight');
            $table->float('goal_fat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weight_settings');
    }
}
