<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RestaurantResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'photo_url' => $this->photo_url,
            'website' => $this->website,
            'is_opening' => $this->is_opening,
            'label' => $this->label,
            'url' => $this->url,
        ];
    }
}
