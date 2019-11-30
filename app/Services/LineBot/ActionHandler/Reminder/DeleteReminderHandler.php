<?php

namespace app\Services\LineBot\ActionHandler\Reminder;

use App\Models\Memory;
use App\Models\TodoList;

class DeleteReminderHandler
{
    /** @var Memory */
    private $memory;
    private $toDoId;

    /**
     * ReminderStateHandler constructor.
     * @param  Memory  $memory
     * @param $toDoId
     */
    public function __construct(Memory $memory, $toDoId)
    {
        $this->memory = $memory;
        $this->toDoId = $toDoId;
    }

    public function handle()
    {
        try {
            if (! TodoList::where('id', $this->toDoId)->exists()) {
                return "提醒編號：{$this->toDoId} 好像不存在喔！";
            }

            return TodoList::where('id', $this->toDoId)->delete()
                ? "你的提醒編號：{$this->toDoId} 已經被刪除囉！"
                : "你的提醒編號：{$this->toDoId} 好像沒有刪除成功喔。";
        } catch (\Exception $e) {
            return "你的提醒編號：{$this->toDoId} 好像沒有刪除成功喔。";
        }
    }
}