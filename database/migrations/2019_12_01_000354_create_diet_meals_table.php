<?php

use App\Models\Line\MealType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietMealsTable extends Migration
{
    const MEAL_TYPE = [
        'BREAKFAST' => '早餐',
        'BRUNCH' => '早午餐',
        'LUNCH' => '午餐',
        'AFTERNOON_TEA' => '下午茶',
        'DINNER' => '晚餐',
        'NIGHT_SNACK' => '宵夜',
        'NIBBLE' => '零食',
        'DESSERT' => '甜點',
    ];

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('meal_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 50);
            $table->string('name', 50);
            $table->timestamps();
        });

        foreach (self::MEAL_TYPE as $mealKey => $mealName) {
            MealType::create([
                'key' => $mealKey,
                'name' => $mealName,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meal_types');
    }
}
