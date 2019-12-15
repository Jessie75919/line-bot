<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessStatusesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('process_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('memory_id');
            $table->string('purpose')->nullable();
            $table->string('command')->nullable();
            $table->string('status')->nullable();
            $table->string('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_statuses');
    }
}
