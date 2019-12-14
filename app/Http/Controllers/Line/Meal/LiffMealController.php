<?php

namespace App\Http\Controllers\Line\Meal;

use App\Http\Controllers\Controller;
use App\Http\Resources\MealReminderCollection;
use App\Http\Resources\MealTypesResource;
use App\Models\Line\MealType;
use App\Models\Memory;
use App\Services\LineBot\Liff\LiffService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LiffMealController extends Controller
{
    public function index(Request $request, LiffService $liffService)
    {
        return view(
            "line.meal.index",
            [
                'liffToken' => $liffService->parsePageToken(config('line.link_of_meal_index')),
                'today' => now('Asia/Taipei')->toDateString(),
                'page' => $request->all()['page'] ?? 'index',
            ]
        );
    }

    public function setting(Memory $memory)
    {
        /* @var Collection $mealReminders */
        $mealReminders = $memory->mealReminders;

        return $mealReminders->count() > 0
            ? new MealReminderCollection($mealReminders)
            : response()->json([]);
    }

    public function mealTypes()
    {
        return new MealTypesResource(MealType::all());
    }
}
