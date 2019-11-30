<?php

namespace App\Console\Commands\Line;

use App\Models\TodoList;
use App\Services\LineBot\ActionHandler\Reminder\LineBotActionReminder;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class NotifyForReminder extends Command
{
    protected $signature = 'line:reminder-schedule';
    protected $description = '執行 Line 提醒功能';

    /**
     * Execute the console command.
     * @param  LineBotPushService  $lineBotPushService
     * @return mixed
     */
    public function handle(LineBotPushService $lineBotPushService)
    {
        $now = now('Asia/Taipei');
        /* @var Collection $todos */
        $todos = TodoList::where('is_sent', 0)
            ->where('send_at', '<=', $now)
            ->where('send_at', '>=', $now->copy()->subSeconds(60))
            ->get();

        if ($todos->isEmpty()) {
            return null;
        }

        foreach ($todos as $todo) {
            \Log::channel('reminder')->info("send message to todoId: {$todo->id}");
            $lineBotPushService->pushMessage($todo->send_channel_id, $todo->message);
            if (! $todo->isRepeat()) {
                $todo->delete();
                continue;
            }

            $this->setNextTime($todo);
        }
    }

    public function setNextTime($todo)
    {
        $repeatPeriod = json_decode($todo->repeat_period, true);
        $period = $repeatPeriod['period'];
        $length = $repeatPeriod['length'];

        $repeatPeriodMethodMap = [
            'D' => 'add'.LineBotActionReminder::PERIOD_MAP['D'].'s',
            'W' => 'add'.LineBotActionReminder::PERIOD_MAP['W'].'s',
            "M" => 'add'.LineBotActionReminder::PERIOD_MAP['M'].'s',
            "m" => 'add'.LineBotActionReminder::PERIOD_MAP['m'].'s',
        ];

        if (! isset($repeatPeriodMethodMap[$period])) {
            return;
        }

        $method = $repeatPeriodMethodMap[$period];
        $sendTimeStr = $todo->send_time
            ->$method($length)
            ->format('Y-m-d H:i:00');

        \Log::channel('reminder')->info("set next time for todoId: {$todo->id} at {$sendTimeStr}");

        $todo->update(['send_time' => $sendTimeStr]);
    }
}
