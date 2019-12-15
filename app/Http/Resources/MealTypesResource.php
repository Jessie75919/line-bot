<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MealTypesResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($mealType) {
            return [
                'id' => $mealType->id,
                'key' => $mealType->key,
                'name' => $mealType->name,
                'time' => $mealType->time,
            ];
        })->toArray();
    }
}
