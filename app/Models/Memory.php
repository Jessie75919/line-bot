<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $messages
 */
class Memory extends Model
{
    protected $fillable = [
        'process_status',
        'channel_id',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function currencies()
    {
        return $this->belongsToMany('\App\Models\Currency');
    }
}
