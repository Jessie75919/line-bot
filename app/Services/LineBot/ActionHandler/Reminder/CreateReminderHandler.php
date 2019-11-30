<?php

namespace app\Services\LineBot\ActionHandler\Reminder;

use App\Models\Memory;
use App\Models\TodoList;
use App\Services\Date\DateParser;
use App\Services\LineBot\LineBotMessageResponser;
use Carbon\Carbon;

class CreateReminderHandler
{
    /** @var Memory */
    private $memory;
    private $msgArr;
    private $repeatPeriod;
    private $toDo;

    /**
     * ReminderStateHandler constructor.
     * @param  Memory  $memory
     * @param $msgArr
     * @param  $repeatPeriod
     */
    public function __construct(Memory $memory, $msgArr, $repeatPeriod)
    {
        $this->memory = $memory;
        $this->msgArr = $msgArr;
        $this->repeatPeriod = $repeatPeriod;
    }

    public function handle()
    {
        [$reminderType, $dateStr, $toDo] = $this->msgArr;
        $dateParser = new DateParser($dateStr);
        $this->toDo = $toDo;

        try {
            /** @var Carbon $targetTime */
            $targetTime = $dateParser->getTargetTime();

            \Log::info(__METHOD__."[".__LINE__."] => TargetTime: ".print_r($targetTime, true));

        } catch (\Exception $e) {
            \Log::error(__METHOD__." => ".$e);
            return " 喔 !? 輸入格式好像有點問題喔～ \n 例如：『 提醒;明天早上九點;吃早餐 』。";
        }

        if ($dateParser->validTimeInThePast($targetTime)) {
            return " 喔 !? 提醒的時間好像已經過期。\n 請輸入『 未來 』的時間才能提醒你喔。";
        }

        $todoList = $this->createReminderOn($targetTime);

        if (is_null($todoList)) {
            return LineBotMessageResponser::ERROR_MESSAGE;
        }

        $repeatPeriod = $todoList->repeat_period;
        $replyMessage = " [提醒時間]\n {$targetTime->toDateTimeString()}\n------------ \n ".
            "[提醒內容]\n {$this->toDo} ";

        $periodMeaning = LineBotActionReminder::getPeriodMeaning($todoList->repeat_period);
        $replyMessage .= $repeatPeriod ? $periodMeaning : "\n";

        return $replyMessage;
    }

    private function createReminderOn($targetTime)
    {
        try {
            return TodoList::create([
                'send_channel_id' => $this->memory->channel_id,
                'receive_channel_id' => $this->memory->channel_id,
                'message' => $this->toDo,
                'send_time' => $targetTime,
                'repeat_period' => $this->repeatPeriod ? json_encode($this->repeatPeriod) : null,
                'is_sent' => 0,
            ]);
        } catch (\Exception $e) {
            \Log::error(__METHOD__." => ".$e);
            return null;
        }
    }
}