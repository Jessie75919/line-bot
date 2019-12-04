<?php

namespace App\Models\Line;

use App\Models\Memory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string status
 * @property array data
 */
class ProcessStatus extends Model
{

    const MEAL = [
        'START' => 'START',
        'SELECT_MEAL_TYPE' => 'SELECT_MEAL_TYPE',
        'READY_ADD' => 'READY_ADD',
    ];

    protected $fillable = [
        'memory_id',
        'purpose',
        'command',
        'status',
        'data',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }

    public function isOnDietStart(): bool
    {
        return $this->status === self::MEAL['START'];
    }

    public function isOnSelectMealType(): bool
    {
        return $this->status === self::MEAL['SELECT_MEAL_TYPE'];
    }

    public function isOnReadyAdd(): bool
    {
        return $this->status === self::MEAL['READY_ADD'];
    }

    public function updateProcessStatus($purpose, $command, $status, $data)
    {
        return $this->update([
            'purpose' => $purpose,
            'command' => $command,
            'status' => $status,
            'data' => $data,
        ]);
    }

    public function mealStart()
    {
        return $this->updateProcessStatus(
            'meal',
            self::MEAL['START'],
            self::MEAL['START'],
            null
        );
    }

    public function mealSelectMealType(int $mealTypeId)
    {
        return $this->updateProcessStatus(
            'meal',
            self::MEAL['SELECT_MEAL_TYPE'],
            self::MEAL['SELECT_MEAL_TYPE'],
            ['meal_type_id' => $mealTypeId]
        );
    }

    public function mealReadySaveTextRecord(int $mealTypeId)
    {
        return $this->updateProcessStatus(
            'meal',
            'ready_add',
            'ready_add',
            ['meal_type_id' => $mealTypeId]
        );
    }
}
