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
}
