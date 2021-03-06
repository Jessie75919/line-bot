<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property float weight
 * @property float fat
 * @property mixed bmi
 */
class Weight extends Model
{
    protected $fillable = [
        'memory_id',
        'weight',
        'fat',
        'bmi',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function hasTodayRecordFor(Memory $memory): bool
    {
        $todayStr = now('Asia/Taipei')->toDateString();
        return $memory->weights()->whereDate('created_at', $todayStr)->first();
    }

    public static function getYesterdayRecord(Memory $memory): ?Weight
    {
        return self::getBeforeRecordsFor($memory, 1)
            ->take(-1)
            ->first();
    }

    public static function getLastTimeRecord(Memory $memory): ?Weight
    {
        return $memory->weights()
            ->whereDate('created_at', '<=', now('Asia/Taipei')->toDateString())
            ->orderBy('created_at', 'desc')
            ->take(2)->get()[1];
    }

    public static function getBeforeRecordsFor(Memory $memory, int $day): Collection
    {
        $todayStr = now('Asia/Taipei')->toDateString();
        $beforeStr = now('Asia/Taipei')->subDays($day)->toDateString();
        return $memory->weights()
            ->whereBetween('created_at', [$beforeStr, $todayStr])
            ->orderBy('created_at')
            ->get();
    }

    public static function saveRecordForToday(Memory $memory, $weight, $fat, $bmi): Model
    {
        $todayStr = now('Asia/Taipei')->toDateString();

        $weightModel = $memory->weights()
            ->whereDate('created_at', $todayStr)
            ->first();

        $payload = [
            'weight' => $weight,
            'fat' => $fat,
            'bmi' => $bmi,
        ];

        if ($weightModel) {
            return tap($weightModel)->update($payload);
        }

        return $memory->weights()->create($payload);
    }

    public function memory()
    {
        return $this->belongsTo('\App\Models\Memory');
    }
}
