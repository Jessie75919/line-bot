<?php

namespace App\Models\BodyTemperature;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 */
class BodyTemperature extends Model
{
    protected $fillable = [
        'month',
        'day',
        'temperature',
        'is_period'
    ];
}
