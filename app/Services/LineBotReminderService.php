<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/4/4星期三
 * Time: 下午6:18
 */

namespace App\Services;

/*  message pattern =>  提醒;TIME;MESSAGE
# input : receive to-do-message
    args : (channelId, [ time, messages])
---------------------------------------------
# process : store to message
    - store to DB ?
    - the specific time - get current time = delayed time
    - set a delayed queue job with delayed time
---------------------------------------------
# output : send the message in specific time
*/
use App\Jobs\TodoJob;
use Carbon\Carbon;
use function dd;
use function dispatch;
use const false;
use InvalidArgumentException;
use Mockery\Exception;
use function now;
use const true;

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
        try {
            $targetTime = Carbon::createFromFormat('Y-m-d H:i', $time, 'Asia/Taipei');
            return isset($targetTime) ? true : false;
        } catch(InvalidArgumentException $exception) {
            return false;
        }
    }


    // get delay time
    private function getDelayTime(string $time): int
    {
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