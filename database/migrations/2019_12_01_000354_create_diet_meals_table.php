<?php

use App\Models\Line\MealType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietMealsTable extends Migration
{
    const MEAL_TYPE = [
        'BREAKFAST' => [
            'name' => '早餐',
            'time' => '08:00',
        ],
        'BRUNCH' => [
            'name' => '早午餐',
            'time' => '10:00',
        ],
        'LUNCH' => [
            'name' => '午餐',
            'time' => '12:00',
        ],
        'AFTERNOON_TEA' => [
            'name' => '下午茶',
            'time' => '15:00',
        ],
        'DINNER' => [
            'name' => '晚餐',
            'time' => '19:00',
        ],
        'NIGHT_SNACK' => [
            'name' => '宵夜',
            'time' => '22:00',
        ],
        'NIBBLE' => [
            'name' => '零食',
            'time' => '20:00',
        ],
        'DESSERT' => [
            'name' => '甜點',
            'time' => '16:00',
        ],
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
            $table->time('time');
            $table->timestamps();
        });

        foreach (self::MEAL_TYPE as $mealKey => $meal) {
            MealType::create([
                'key' => $mealKey,
                'name' => $meal['name'],
                'time' => $meal['time'],
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
