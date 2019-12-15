<?php

namespace App\Repository\LineBot\Meal;

use App\Models\Line\Meal;
use Illuminate\Support\Collection;

class MealRepo implements IMealRepo
{
    public function getByMemoryId(int $memoryId): Collection
    {
        return Meal::with('mealType')
            ->where('memory_id', $memoryId)
            ->whereNotNull('image_url')
            ->orderBy('save_date', 'desc')
            ->get();
    }
}