<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 */
class TodoList extends Model
{
    protected $fillable = [
        'send_channel_id',
        'receive_channel_id',
        'message',
        'send_time',
        'is_sent'
    ];

}
