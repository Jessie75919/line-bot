<?php

namespace App\Models\Line;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Storage;

/**
 * @property int meal_type_id
 * @property mixed memory
 * @property string image_url
 * @property mixed save_date
 * @property mixed mealType
 */
class Meal extends Model
{
    const LINE_MEAL = 'line-meal';
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

    /**
     * @return string
     */
    public function getTodayMealFilePath(): string
    {
        $today = now('Asia/Taipei')->toDateString();
        $uuid = (string) Str::uuid();
        return "{$this->memory->id}/{$today}/{$this->meal_type_id}/{$uuid}.jpeg";
    }

    public function getTodayImageUrl(): string
    {
        if ($this->image_url) {
            return Storage::disk(self::LINE_MEAL)
                ->temporaryUrl(
                    $this->image_url,
                    now('Asia/Taipei')->addMinutes(15)
                );
        }
        return '';
    }

    /**
     * @param  mixed  $image
     * @return bool
     */
    public function storeFile($image): bool
    {
        $path = $this->getTodayMealFilePath();
        Storage::disk(self::LINE_MEAL)->put($path, $image);

        return $this->update([
            'image_url' => $path,
            'save_date' => now('Asia/Taipei')->toDateString(),
        ]);
    }
}
