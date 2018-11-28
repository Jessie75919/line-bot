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
        'year',
        'month',
        'day',
        'temperature',
        'user_id',
        'is_period'
    ];
}
