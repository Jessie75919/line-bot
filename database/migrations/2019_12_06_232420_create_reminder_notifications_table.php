<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReminderNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('meal_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('memory_id');
            $table->unsignedInteger('meal_type_id');
            $table->time('remind_at');
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
        Schema::dropIfExists('meal_reminders');
    }
}
