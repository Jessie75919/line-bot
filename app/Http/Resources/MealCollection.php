<?php

namespace App\Http\Resources;

use App\Models\Line\Meal;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MealCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection
            ->map(function (Meal $meal) {
                return [
                    'image_url' => $meal->getTodayImageUrl(),
                    'save_date' => $meal->save_date,
                    'meal_type' => [
                        'key' => $meal->mealType->key,
                        'name' => $meal->mealType->name,
                    ],
                ];
            })
            ->toArray();
    }
}
