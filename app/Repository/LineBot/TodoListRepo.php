<?php

namespace App\Repository\LineBot;

use Carbon\Carbon;
use App\Models\TodoList;

class TodoListRepo
{
    public function getByChannelId($channelId)
    {
        return TodoList::where('send_channel_id', $channelId)
                       ->where('is_sent', 0)
                       ->where('send_time', '>', Carbon::now('Asia/Taipei'))
                       ->get(['id', 'message', 'send_time']);
    }
}
