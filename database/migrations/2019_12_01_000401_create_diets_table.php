<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('memory_id');
            $table->unsignedInteger('meal_type_id');
            $table->string('photo_url')->nullable();
            $table->string('comment')->nullable();
            $table->string('line_message_id')->nullable();
            $table->date('save_date');
            $table->timestamps();
            $table->index('memory_id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals');
    }
}
