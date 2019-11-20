<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWeightSetting extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('weight_settings', function (Blueprint $table) {
            $table->boolean('enable_notification')->default(1)->after('goal_fat');
            $table->unsignedTinyInteger('notify_day')->default(0)->after('enable_notification');
            $table->time('notify_at')->default('19:00')->after('notify_day');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('weight_settings', function (Blueprint $table) {
            $table->dropColumn('enable_notification');
            $table->dropColumn('notify_day');
            $table->dropColumn('notify_at');
        });
    }
}
