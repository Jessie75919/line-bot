<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Weight extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($weight) {
            return [
                'weight' => $weight->weight,
                'fat' => $weight->fat,
                'date' => $weight->created_at->format('m/d'),
            ];
        })->toArray();
    }
}
