<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/4/4星期三
 * Time: 下午6:18
 */

namespace App\Services;


use App\Jobs\TodoJob;
use Carbon\Carbon;
use const false;
use InvalidArgumentException;

class LineBotReminderService
{
    private $channelId;
    private $message;


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


    public function handle(): bool
    {
        if($this->validTimeFormat($this->message[0])) {
            $delayTime = $this->getDelayTime($this->message[0]);
            return $this->setQueue($delayTime);
        }
        return false;
    }


    private function validTimeFormat($time):bool
    {
        $time = $this->checkForAliasDay($time);

        try {
            $targetTime = Carbon::createFromFormat('Y-m-d H:i',$time, 'Asia/Taipei');
            return isset($targetTime) ? true : false;
        } catch(InvalidArgumentException $exception) {
            return false;
        }
    }


    private function checkForAliasDay($time):string
    {
        $times = explode(' ', $time);
        $date = null ;

        switch($times[0]) {
            case '今天':
                $date = Carbon::now('Asia/Taipei')->toDateString();
                break;
            case '明天':
                $date = Carbon::now('Asia/Taipei')->addDay(1)->toDateString();
                break;
            case '後天':
                $date = Carbon::now('Asia/Taipei')->addDay(2)->toDateString();
                break;
            default:
                $date = $times[0];
                break;
        }
        return "{$date} $times[1]";
    }

    // get delay time
    private function getDelayTime(string $time): int
    {
        $time = $this->checkForAliasDay($time);

        \Log::info('time = ' . ($time));
        $current    = Carbon::now('Asia/Taipei');
        $targetTime = Carbon::createFromFormat('Y-m-d H:i', $time, 'Asia/Taipei');
        \Log::info('$current = ' . print_r($current, true));
        \Log::info('targetTime = ' . print_r($targetTime, true));

        $diffTime = $targetTime->diffInSeconds($current);
        \Log::info(" = {$diffTime}");
        return $diffTime;
    }


    private function setQueue($delayTime): bool
    {
        try {
            \Log::info("setQueueJob for {$this->channelId} , {$this->message[1]}");
            dispatch(new TodoJob($this->channelId, $this->message[1]))
                ->delay(now('Asia/Taipei')->addSeconds($delayTime));
            return true;

        } catch(Exception $e) {
            \Log::error("error => {$e->getMessage()}");
            return false;
        }

    }


}