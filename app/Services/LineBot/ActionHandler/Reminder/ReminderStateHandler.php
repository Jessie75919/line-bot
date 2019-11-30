<?php

namespace app\Services\LineBot\ActionHandler\Reminder;

use App\Models\Memory;
use App\Repository\LineBot\TodoListRepo;

class ReminderStateHandler
{
    /** @var Memory */
    private $memory;

    /**
     * ReminderStateHandler constructor.
     * @param  Memory  $memory
     */
    public function __construct(Memory $memory)
    {
        $this->memory = $memory;
    }

    public function handle()
    {
        $responseText = null;
        /* @var TodoListRepo $todoListRepo */
        $todoListRepo = app(TodoListRepo::class);
        $todos = $todoListRepo->getByChannelId($this->memory->channel_id);

        if (count($todos) === 0) {
            return '目前沒有任何提醒喔！';
        }

        foreach ($todos as $todo) {
            $responseText .= " 編號：{$todo->id} \n 提醒內容：{$todo->message} \n 提醒時間：{$todo->send_time} \n ";
            $periodMeaning = LineBotActionReminder::getPeriodMeaning($todo->repeat_period);
            $responseText .= $periodMeaning ? $periodMeaning : "\n";
        }

        return $responseText;
    }
}