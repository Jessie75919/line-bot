<?php

namespace App\Models;
;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int $id
 * @property \Carbon\Carbon $updated_at
 */
class TodoList extends Model
{
    protected $fillable = [
        'send_channel_id',
        'receive_channel_id',
        'repeat_period',
        'message',
        'send_time',
        'is_sent',
    ];

    protected $dates = [
        'send_time',
    ];

    public function isRepeat(): bool
    {
        return isset($this->repeat_period);
    }
}
