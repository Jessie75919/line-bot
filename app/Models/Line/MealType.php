<?php

namespace App\Models\Line;

use Illuminate\Database\Eloquent\Model;

class MealType extends Model
{
    protected $fillable = ['key', 'name', 'time'];

    public function mealReminders()
    {
        return $this->hasMany(MealReminder::class);
    }
}
