<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property mixed memory_id
 * @property mixed meal_type_id
 * @property mixed remind_at
 */
class MealRemindersResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'memory_id' => $this->memory_id,
            'meal_type_id' => $this->meal_type_id,
            'remind_at' => $this->remind_at->toDateTimeString(),
        ];
    }
}
