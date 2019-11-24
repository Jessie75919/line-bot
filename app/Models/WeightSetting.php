<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightSetting extends Model
{
    protected $fillable = [
        'height',
        'goal_weight',
        'goal_fat',
        'memory_id',
        'enable_notification',
        'notify_days',
        'notify_at',
    ];

    public function memory()
    {
        return $this->belongsTo('\App\Models\Memory');
    }
}
