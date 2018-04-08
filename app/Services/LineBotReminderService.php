<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/4/4星期三
 * Time: 下午6:18
 */

namespace App\Services;


use App\Jobs\TodoJob;
use App\TodoList;
use Carbon\Carbon;
use function count;
use function dd;
use Exception;
use const false;
use InvalidArgumentException;
use function is_string;
use const null;
use function preg_match;
use function preg_replace;
use function strlen;
use function strpos;
use function substr;
use function time;
use function trim;
use const true;

class LineBotReminderService
{
    private $channelId;
    private $message;
    private $todoListId;
    const PAST_TIME_ERROR = 'PAST_TIME_ERROR';
    const FORMAT_ERROR    = 'FORMAT_ERROR';
    const SUCCESS         = 'SUCCESS';
    const ERROR           = 'ERROR';


    /**
     * LineBotReminderService constructor.
     * @param $channelId
     * @param $message
     */
    public function __construct($channelId, $message)
    {
        \Log::info('message = ' . print_r($message, true));
        $this->channelId = $channelId;
        $this->message   = $message;
    }


    public function handle(): string
    {

        // get TargetTime
        try {
            /** @var Carbon $targetTime */
            $targetTime = $this->getTargetTime($this->message[0]);
             \Log::info("targetTime => " . print_r($targetTime , true));
        } catch(\Exception $e) {
            $e->getMessage();
            return self::FORMAT_ERROR;
        }


        if($this->validTimeInThePast($targetTime)) {
            \Log::info("validTimeInThePast => { true }");
            return self::PAST_TIME_ERROR;
        }

        $delayTime = $this->getDelayTime($targetTime);

        if($this->storeToDB($targetTime)) {
            return $this->setQueue($delayTime, $this->todoListId) ? self::SUCCESS : self::ERROR;
        }

        return self::ERROR;
    }


    private function getTargetTime($time)
    {
        $times = explode(' ', $time);
        \Log::info("times => " . print_r($times, true));
        $date = Carbon::now('Asia/Taipei')->toDateString();

        if(count($times) == 2) {
            // 如果不是2018開頭 ==> 今天...
            if(strpos($times[0], '20') === false) {
                $dateData = $this->dateTimeAnalyze($date, $times[0], $times[1]);
                return $this->createTargetTime($dateData[0], $dateData[1], 0);
            } else { // 2018-07-02 格式開頭
                $dateTime = "$times[0] $times[1]";
                return $this->createTargetTime($dateTime, false, 0);
            }
        }

        $addDays = null;

        if(count($times) === 3) {
            $addDays = $this->getAddDaysCount($times[0]);
        }

        //今天 早上 9點45分
        $dateData = $this->dateTimeAnalyze($date, $times[1], $times[2]);

        return $this->createTargetTime($dateData[0], $dateData[1], $addDays);
    }


    // get delay time
    private function getDelayTime($targetTime): int
    {
        $current = Carbon::now('Asia/Taipei');

        \Log::info('$current = ' . print_r($current, true));
        \Log::info('targetTime = ' . print_r($targetTime, true));

        $diffTime = $targetTime->diffInSeconds($current);
        \Log::info(" = {$diffTime}");
        return $diffTime;
    }


    private function setQueue($delayTime, $todoListId): bool
    {
        try {
            \Log::info("setQueueJob for {$this->channelId} , {$this->message[1]}");

            dispatch(new TodoJob($this->channelId, $this->message[1], $todoListId))
                ->delay(now('Asia/Taipei')->addSeconds($delayTime));
            return true;

        } catch(Exception $e) {
            \Log::error("error => {$e->getMessage()}");
            return false;
        }
    }


    public function validTimeInThePast($targetTime): bool
    {
        try {
            \Log::info("targetTime(validTimeInThePast) => " . print_r($targetTime, true));
            if($targetTime->lessThan(Carbon::now('Asia/Taipei'))) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            $e->getMessage();
            return false;
        }
    }


    public function isNeedToPlus12($timeInterval): bool
    {
        switch($timeInterval) {
            case '早上':
                return false;
                break;
            case '上午':
                return false;
                break;
            case '中午':
                return false;
                break;
            case '下午':
                return true;
                break;
            case '晚上':
                return true;
                break;
            default:
                return false;
                break;
        }
    }


    private function timeAnalyze($time): string
    {
        $time = trim($time);
        \Log::info("time => {$time}");

        // check 5點
        $pattern = '/([0-1]*[0-9]+)點$/';
        if(preg_match($pattern, $time) == 1) {
            $time = preg_replace($pattern, "$1:00", $time);
        }

        // check 5點半
        $pattern = '/([0-1]*[0-9]+)點半$/';
        if(preg_match($pattern, $time) == 1) {
            $time = preg_replace($pattern, "$1:30", $time);
        }

        // check 5點30分
        $pattern = '/([0-1]*[0-9]+)點([0-1]*[0-9]+)分$/';
        if(preg_match($pattern, $time) == 1) {
            $time = preg_replace($pattern, "$1:$2", $time);
        }

        \Log::info("processed time => {$time}");

        // check 5：30
        $pattern = '/([0-1]*[0-9]+)：([0-1]*[0-9]+)/';
        if(preg_match($pattern, $time) == 1) {
            $time = preg_replace($pattern, "$1:$2", $time);
        }

        return $time;
    }


    private function storeToDB($targetTime)
    {
        try {
            $todo = TodoList::create([
                'send_channel_id'    => $this->channelId,
                'receive_channel_id' => $this->channelId,
                'message'            => $this->message[1],
                'send_time'          => $targetTime,
                'is_sent'            => 0
            ]);
            \Log::info("todoId => {$todo->id}");

            $this->todoListId = $todo->id;
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }


    private function createTargetTime(string $dateTime, bool $isNeedPlus12Hours, int $addDays)
    {
        $targetTime = $isNeedPlus12Hours
            ? Carbon::createFromFormat('Y-m-d H:i', $dateTime, 'Asia/Taipei')->addDays($addDays)->addHours(12)
            : Carbon::createFromFormat('Y-m-d H:i', $dateTime, 'Asia/Taipei')->addDays($addDays);

        return $targetTime;
    }


    private function getAddDaysCount(string $dateAlias): int
    {
        switch($dateAlias) {
            case '今天':
                $addDays = 0;
                break;
            case '明天':
                $addDays = 1;
                break;
            case '後天':
                $addDays = 2;
                break;
            default:
                $addDays = 0;
                break;
        }
        return $addDays;
    }


    private function dateTimeAnalyze($date, $dateAlias, $time):array
    {
        $isNeedPlus12 = $this->isNeedToPlus12($dateAlias);
        \Log::info("isNeedToPlus12 => {$isNeedPlus12}");
        $time = $this->timeAnalyze($time);

        return ["{$date} {$time}", $isNeedPlus12];
    }


}