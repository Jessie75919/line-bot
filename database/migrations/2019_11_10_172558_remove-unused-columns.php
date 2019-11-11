<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedColumns extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('memories', function (Blueprint $table) {
            $table->dropColumn('echo2');
            $table->dropColumn('save_to_received');
            $table->dropColumn('save_to_reply');
            $table->dropColumn('is_talk');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('memories', function (Blueprint $table) {
            $table->string('save_to_received')->nullable();
            $table->string('save_to_reply')->nullable();
            $table->string('echo2')->nullable();
            $table->string('is_talk')->nullable();
        });
    }
}
