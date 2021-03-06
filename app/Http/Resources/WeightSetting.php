<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeightSetting extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'height' => $this->height,
            'goal_fat' => $this->goal_fat,
            'goal_weight' => $this->goal_weight,
            'enable_notification' => $this->enable_notification,
            'notify_days' =>
                is_null($this->notify_days)
                    ? []
                    : explode(',', $this->notify_days),
            'notify_at' => $this->notify_at,
        ];
    }
}
