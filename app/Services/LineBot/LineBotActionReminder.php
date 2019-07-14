<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/4/4星期三
 * Time: 下午6:18
 */

namespace App\Services\LineBot;

use Exception;
use Carbon\Carbon;
use App\Jobs\TodoJob;
use App\Models\TodoList;
use App\Services\Date\DateParser;
use App\Repository\LineBot\TodoListRepo;
use function count;
use const true;
use const false;

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

    private $payload;
    private $todoListRepo;
    /** * @var LineBotMessageResponser */
    private $messageResponser;


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
                'is_sent'            => 0
            ]);

            \Log::info("todoId => {$todo->id}");

            return $todo;
        } catch (\Exception $e) {
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
        $breakdownMessage = LineBotMessageReceiver::breakdownMessage($originMessage);

        $purpose = trim($breakdownMessage[1]);

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
            $responseText .= " \n";
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

            $successMessage = " [提醒時間]\n {$targetTime->toDateTimeString()}\n============= \n " .
                "[提醒內容]\n {$this->payload['message']['value']}";
            return $isSuccess
                ? $successMessage
                : LineBotMessageResponser::ERROR_MESSAGE;
        } else {
            return LineBotMessageResponser::ERROR_MESSAGE;
        }
    }
}
