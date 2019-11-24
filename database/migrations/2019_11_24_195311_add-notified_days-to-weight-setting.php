<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifiedDaysToWeightSetting extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('weight_settings', function (Blueprint $table) {
            $table->string('notify_days', 50)->after('enable_notification')->nullable();
            $table->dropColumn('notify_day');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('weight_settings', function (Blueprint $table) {
            $table->unsignedInteger('notify_day');
            $table->dropColumn('notify_days');
        });
    }
}
