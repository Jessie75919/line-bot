<?php

namespace App\Services\LineBot\ActionHandler;

use Exception;
use Carbon\Carbon;
use App\Jobs\TodoJob;
use App\Models\TodoList;
use App\Services\Date\DateParser;
use App\Repository\LineBot\TodoListRepo;
use App\Services\LineBot\TypePayloadHandler;
use App\Services\LineBot\LineBotMessageResponser;

class LineBotActionReminder implements LineBotActionHandlerInterface
{
    const PAST_TIME_ERROR = 'PAST_TIME_ERROR';
    const FORMAT_ERROR    = 'FORMAT_ERROR';
    const SUCCESS         = 'SUCCESS';
    const ERROR           = 'ERROR';
    const STATE           = "STATE";
    const REMINDER_STATE  = "Reminder-State";
    const REMINDER        = 'reminder';
    const REMINDER_DELETE = "Reminder-Delete";
    const PERIOD_MAP      = [
        'D' => 'Day',
        'W' => 'Week',
        "M" => 'Minute'
    ];

    private $payload;
    private $todoListRepo;
    /** * @var LineBotMessageResponser */
    private $messageResponser;
    private $repeatPeriod = null;


    /**
     * LineBotLearnService constructor.
     * @param array        $payload
     * @param TodoListRepo $todoListRepo
     */
    public function __construct(array $payload, TodoListRepo $todoListRepo)
    {
        $this->payload = $payload;
        $this->todoListRepo = $todoListRepo;
        $this->messageResponser = new LineBotMessageResponser($this->payload['channelId'], 'response');
        $this->updateDetailPurpose();
    }


    public function handle()
    {
        $replyContent = null;

        switch ($this->payload['purpose']) {
            case self::REMINDER_STATE:
                $replyContent = $this->reminderStateHandler();
                break;
            case self::REMINDER_DELETE:
                $replyContent = $this->deleteReminderHandler();
                break;
            case self::REMINDER:
                $replyContent = $this->setReminderHandler();
                break;
        }

        return $this->messageResponser
            ->setContent($replyContent)
            ->responseToUser();
    }


    private function setQueue($delayTime, $todoListId): bool
    {
        try {
            \Log::info("setQueueJob for {$this->payload['channelId']} , " .
                "{$this->payload['message']['value']}");

            dispatch(
                new TodoJob(
                    $this->payload['channelId'],
                    $this->payload['message']['value'],
                    $todoListId
                )
            )->delay(now('Asia/Taipei')->addSeconds($delayTime));

            return true;
        } catch (Exception $e) {
            \Log::error("error => {$e->getMessage()}");
            return false;
        }
    }


    private function storeToDB($targetTime)
    {
        try {
            $todo = TodoList::create([
                'send_channel_id'    => $this->payload['channelId'],
                'receive_channel_id' => $this->payload['channelId'],
                'message'            => $this->payload['message']['value'],
                'send_time'          => $targetTime,
                'repeat_period'      => $this->repeatPeriod ? json_encode($this->repeatPeriod) : null,
                'is_sent'            => 0
            ]);

            \Log::info("todoId => {$todo->id}");

            return $todo;
        } catch (\Exception $e) {
            \Log::error(__METHOD__ . " => " . $e);
            return null;
        }
    }


    private function deleteReminderHandler()
    {
        $todoId = $this->payload['message']['value'];

        try {
            return TodoList::where('id', $todoId)->delete()
                ? "你的提醒編號：{$todoId} 已經被刪除囉！"
                : "你的提醒編號：{$todoId} 好像沒有刪除成功喔。";
        } catch (\Exception $e) {
            return "你的提醒編號：{$todoId} 好像沒有刪除成功喔。";
        }
    }


    private function updateDetailPurpose()
    {
        $originMessage = $this->payload['message']['origin'];
        $breakdownMessage = TypePayloadHandler\TextTypePayloadHandler::breakdownMessage($originMessage);

        $purposeKey = $breakdownMessage[0];
        $pattern = '/remR(.*)/m';
        if (preg_match($pattern, $purposeKey)) {
            $repeatPeriod = preg_replace($pattern, '$1', $purposeKey);
            // [3,W(week)]
            $numberWithPeriod =
                explode(
                    ',',
                    preg_replace(
                        '/(\d?)(\w?)/m',
                        '$1,$2',
                        $repeatPeriod
                    )
                );
            $this->repeatPeriod = [
                'period' => $numberWithPeriod[1],
                'length' => $numberWithPeriod[0] === '' ? 1 : (int)$numberWithPeriod[0]
            ];
        }

        $purpose = $breakdownMessage[1];

        switch ($purpose) {
            case $purpose === 'all' || $purpose === '所有提醒':
                $this->payload['purpose'] = self::REMINDER_STATE;
                break;
            case $purpose === '刪除' || $purpose === 'del':
                $this->payload['purpose'] = self::REMINDER_DELETE;
                break;
            default:
                $this->payload['purpose'] = self::REMINDER;
        }
    }


    /**
     * @return string|null
     */
    private function reminderStateHandler(): string
    {
        $responseText = null;
        $todos = $this->todoListRepo->getByChannelId($this->payload['channelId']);

        if (count($todos) == 0) {
            return '目前沒有任何提醒喔！';
        }

        foreach ($todos as $todo) {
            $responseText .= " 編號：{$todo->id} \n 提醒內容：{$todo->message} \n 提醒時間：{$todo->send_time} \n ";

            $periodMeaning = $this->getPeriodMeaning($todo->repeat_period);
            $responseText .= $periodMeaning ? $periodMeaning : "\n";
        }

        \Log::info("responseText => {$responseText}");

        return $responseText;
    }


    private function setReminderHandler()
    {
        $dateStr = $this->payload['message']['key'];
        $dateParser = new DateParser($dateStr);

        try {
            /** @var Carbon $targetTime */
            $targetTime = $dateParser->getTargetTime();
        } catch (\Exception $e) {
            \Log::error(__METHOD__ . " => " . $e);
            return " 喔 !? 輸入格式好像有點問題喔～ \n 例如：『 提醒;明天早上九點;吃早餐 』。";
        }

        if ($dateParser->validTimeInThePast($targetTime)) {
            return " 喔 !? 提醒的時間好像已經過期。\n 請輸入『 未來 』的時間才能提醒你喔。";
        }

        $todoList = $this->storeToDB($targetTime);

        if (isset($todoList)) {
            $delayTime = $dateParser->getDelayTime($targetTime);
            $isSuccess = $this->setQueue($delayTime, $todoList->id);

            $repeatPeriod = $todoList->repeat_period;
            $successMessage = " [提醒時間]\n {$targetTime->toDateTimeString()}\n------------ \n " .
                "[提醒內容]\n {$this->payload['message']['value']} ";

            $periodMeaning = $this->getPeriodMeaning($todoList->repeat_period);
            $successMessage .= $repeatPeriod ? $periodMeaning : "\n";

            return $isSuccess
                ? $successMessage
                : LineBotMessageResponser::ERROR_MESSAGE;
        } else {
            return LineBotMessageResponser::ERROR_MESSAGE;
        }
    }


    /**
     * @param $repeatPeriod
     * @return string
     */
    private function getPeriodMeaning($repeatPeriod): ?string
    {
        if (! $repeatPeriod) {
            return null;
        }

        $repeatPeriod = json_decode($repeatPeriod, true);

        return "(" . $repeatPeriod['length'] . self::PERIOD_MAP[$repeatPeriod['period']] . " 重複一次) \n";
    }
}
