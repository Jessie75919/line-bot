<?php

use App\Utilities\DateTools;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $year = DateTools::thisLunarYear();

        Schema::table('body_temperatures', function (Blueprint $table) use ($year){
            $table->tinyInteger('year')->after('user_id')->default($year);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('body_temperatures', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
}
