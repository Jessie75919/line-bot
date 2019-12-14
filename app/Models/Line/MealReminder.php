<?php

namespace App\Models\Line;

use App\Models\Memory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/* @property int id */
/* @property int memory_id */
/* @property Carbon remind_at */
class MealReminder extends Model
{
    protected $fillable = [
        'memory_id',
        'meal_type_id',
        'remind_at',
    ];

    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }
}
