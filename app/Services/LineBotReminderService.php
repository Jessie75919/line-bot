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
use function count;
use Exception;
use const false;
use InvalidArgumentException;
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
    private $isNeedPlus12;
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


    public function handle(): bool
    {
        if(!$this->validTimeFormat($this->message[0])) {
            return self::FORMAT_ERROR;
        }

        if($this->validTimeInThePast($this->message[0])) {
            \Log::info("validTimeInThePast => { true }");
            return self::PAST_TIME_ERROR;
        }

        $delayTime = $this->getDelayTime($this->message[0]);

        return $this->setQueue($delayTime) ? self::SUCCESS : self::ERROR;

    }


    private function validTimeFormat($time):bool
    {
        $time = $this->checkForAliasDay($time);

        \Log::info("time in validTimeFormat => {$time}");

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
         \Log::info("times => " . print_r($times , true));
        $date  = null;

        if(count($times) == 2) {
            // 如果不是2018開頭 ==> 今天...
            if(strpos($times[0], '20') === false) {
                $date = Carbon::now('Asia/Taipei')->toDateString();

                return "{$date} $times[1]";
            }else { // 2018-07-02 格式開頭
                return "$times[0] $times[1]";
            }
        }


        if(count($times) === 3) {
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
        }

        //今天 早上 9點45分
        $this->isNeedPlus12 = $this->isNeedToPlus12($times[1]);
        \Log::info("isNeedToPlus12 => {$this->isNeedPlus12}");
        $time = $this->timeAnalyze($times[2]);

        $dateTime = "{$date} $time";

        \Log::info("dateTime => {$dateTime}");

        return $dateTime;
    }

    // get delay time
    private function getDelayTime(string $time): int
    {
        $time = $this->checkForAliasDay($time);

        \Log::info('time = ' . ($time));
        $current    = Carbon::now('Asia/Taipei');
        $targetTime = $this->isNeedPlus12
                    ? Carbon::createFromFormat('Y-m-d H:i', $time, 'Asia/Taipei')->addHours(12)
                    : Carbon::createFromFormat('Y-m-d H:i', $time, 'Asia/Taipei');

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


    public function validTimeInThePast($time):bool
    {
        \Log::info("time in validTimeBefore => {$time}");

        $time = $this->checkForAliasDay($time);

        try {
            $targetTime = Carbon::createFromFormat('Y-m-d H:i', $time, 'Asia/Taipei');
            if($targetTime->lessThan(Carbon::now('Asia/Taipei'))) {
                return true;
            }
            return false;
        } catch(Exception $e) {
            $e->getMessage();
            return false;
        }
    }


    public  function isNeedToPlus12($timeInterval):bool
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




    private function timeAnalyze($time):string
    {
        $time = trim($time);

        \Log::info("time => {$time}");

        // check 5點
        $pattern = '/([0-1]*[0-9]+)點$/' ;
        if($this->regularExpressionCheck($pattern, $time) == 1) {
            $time = preg_replace($pattern, "$1:00", $time);
        }

        // check 5點半
        $pattern = '/([0-1]*[0-9]+)點半$/' ;
        if($this->regularExpressionCheck($pattern, $time) == 1) {
            $time =  preg_replace($pattern, "$1:30", $time);
        }

        // check 5點30分
        $pattern = '/([0-1]*[0-9]+)點([0-1]*[0-9]+)分$/' ;
        if($this->regularExpressionCheck($pattern, $time) == 1) {
            $time =  preg_replace($pattern, "$1:$2", $time);
        }

        \Log::info("processed time => {$time}");

        return $time;

    }


    private function regularExpressionCheck($pattern, $time):int
    {
        return preg_match($pattern, $time);
    }


}