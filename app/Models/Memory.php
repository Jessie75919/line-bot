<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $messages
 */
class Memory extends Model
{
    protected $fillable = [
        'is_talk',
        'save_to_received',
        'process_status',
        'channel_id',
        'save_to_reply',
        'echo2',
    ];


    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
