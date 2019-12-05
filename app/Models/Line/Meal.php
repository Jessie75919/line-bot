<?php

namespace App\Models\Line;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int meal_type_id
 */
class Meal extends Model
{
    protected $fillable = [
        'memory_id',
        'meal_type_id',
        'image_url',
        'comment',
        'save_date',
    ];

    public function memory()
    {
        return $this->belongsTo('\App\Models\Memory');
    }

    public function mealType()
    {
        return $this->belongsTo('\App\Models\Line\MealType');
    }
}
