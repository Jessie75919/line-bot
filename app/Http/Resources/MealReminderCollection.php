<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property mixed memory_id
 * @property mixed meal_type_id
 * @property mixed remind_at
 */
class MealReminderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($mealReminder) {
            [$hour, $minute,] = explode(':', $mealReminder->remind_at);
            return [
                'meal_type_id' => $mealReminder->meal_type_id,
                'hour' => $hour,
                'minute' => $minute,
            ];
        })->toArray();
    }
}
