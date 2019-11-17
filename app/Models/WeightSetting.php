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
    ];

    public function memory()
    {
        return $this->belongsTo('\App\Models\Memory');
    }
}
