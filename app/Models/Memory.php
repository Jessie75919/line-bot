<?php

namespace App\Models;

use App\Models\Line\Meal;
use App\Models\Line\MealReminder;
use App\Models\Line\MealType;
use App\Models\Line\ProcessStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $messages
 * @property int channel_id
 * @property string process_status
 * @property ProcessStatus processStatus
 * @property mixed mealReminders
 */
class Memory extends Model
{
    protected $fillable = [
        'process_status',
        'channel_id',
    ];

    public static function getByChannelId($channelId): ?Memory
    {
        return Memory::where('channel_id', $channelId)->first();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function currencies()
    {
        return $this->belongsToMany('\App\Models\Currency');
    }

    public function weights()
    {
        return $this->hasMany('\App\Models\Weight');
    }

    public function weightSetting()
    {
        return $this->hasOne('\App\Models\WeightSetting');
    }

    public function meal()
    {
        return $this->hasMany('\App\Models\Line\Meal');
    }

    public function getRouteKeyName()
    {
        return 'channel_id';
    }

    public function processStatus()
    {
        return $this->hasOne(ProcessStatus::class);
    }

    public function mealReminders()
    {
        return $this->hasMany(MealReminder::class);
    }

    /**
     * @param $mealTypeId
     * @return Meal
     * @throws \Exception
     */
    public function getTodayMealByType($mealTypeId): Meal
    {
        if (! MealType::where('id', $mealTypeId)->exists()) {
            throw new \Exception("Meal type is not existed");
        }

        return Meal::firstOrCreate([
            'memory_id' => $this->id,
            'meal_type_id' => $mealTypeId,
            'save_date' => now('Asia/Taipei')->toDateString(),
        ]);
    }

    public function getProcessStatus(): ProcessStatus
    {
        return $this->processStatus()->firstOrCreate([]);
    }

}
