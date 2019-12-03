<?php

namespace App\Services\LineBot\ActionHandler\Reminder;

use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;

class LineBotActionReminder extends LineBotActionHandler
{
    const REMINDER_STATE = "Reminder-State";
    const REMINDER = 'reminder';
    const REMINDER_DELETE = "Reminder-Delete";
    const PERIOD_MAP = [
        'D' => 'Day',
        'W' => 'Week',
        "M" => 'Month',
        "m" => 'Minute',
    ];
    private $repeatPeriod = null;
    private $toDo;

    /**
     * LineBotLearnService constructor.
     * @param  Memory  $memory
     * @param $message
     */
    public function __construct(Memory $memory, $message)
    {
        parent::__construct($memory, $message);
    }

    /**
     * @param $repeatPeriod
     * @return string
     */
    public static function getPeriodMeaning($repeatPeriod): ?string
    {
        if (! $repeatPeriod) {
            return null;
        }

        $repeatPeriod = json_decode($repeatPeriod, true);

        return "(".$repeatPeriod['length'].self::PERIOD_MAP[$repeatPeriod['period']]." 重複一次) \n";
    }

    public function handle()
    {
        $msgArr = $this->parseMessage($this->message);
        $command = $this->getDetailCommand($msgArr);

        switch ($command) {
            case self::REMINDER_STATE:
                $message = (new ReminderStateHandler($this->memory))->handle();
                break;
            case self::REMINDER_DELETE:
                $message = (new DeleteReminderHandler($this->memory, $this->toDo))->handle();
                break;
            case self::REMINDER:
                $message = (new CreateReminderHandler($this->memory, $msgArr, $this->repeatPeriod))->handle();
                break;
            default:
                $message = '😥️ 指令好像輸入錯誤囉！';
        }

        return $this->reply($message);
    }

    private function getDetailCommand($msgArr)
    {
        $reminderMode = $msgArr[0];
        $command = $msgArr[1];
        $this->toDo = $msgArr[2] ?? null;

        if ($reminderMode === 'rem') {
            switch ($command) {
                case $command === 'all' || $command === '所有提醒':
                    return self::REMINDER_STATE;
                    break;
                case $command === '刪除' || $command === 'del':
                    return self::REMINDER_DELETE;
                default:
                    return self::REMINDER;
            }
        }

        /* ex: remR3W */
        $pattern = '/remR(.*)/m';
        if (preg_match($pattern, $reminderMode)) {
            $repeatPeriod = preg_replace($pattern, '$1', $reminderMode);
            // [3,W(week)]
            $numberWithPeriod = explode(',', preg_replace('/(\d?)(\w?)/m', '$1,$2', $repeatPeriod));

            $this->repeatPeriod = [
                'length' => $numberWithPeriod[0] === '' ? 1 : (int) $numberWithPeriod[0],
                'period' => $numberWithPeriod[1],
            ];

            return self::REMINDER;
        }
    }
}
