<?php

namespace App\Services\Date;

use Exception;
use Carbon\Carbon;

class DateParser
{
    const DATE_PATTERNS      = [
        'FORMAL_DATE_PATTERN'      => "/(2\d{3})(-|\\|\s|,|\/){1}(\d{2})(-|\\|\s|,|\/){1}(\d{2})\s+(.*)/mu",
        'TIME_ALIAS_PATTERN'       => "/(早上|上午|中午|下午|晚上)?(.*)/mu",
        'CROSS_DAYS_ALIAS_PATTERN' => "/(今天|明天|後天)(.*)/mu",
        'WEEKDAY_ALIAS_PATTERN'    => "/(下*)((星期|禮拜){1})(\w{1})(.*)/mu"
    ];
    const CHINESE_NUMBER_MAP = [
        '日'  => 7,
        '一'  => 1,
        '二'  => 2,
        '三'  => 3,
        '四'  => 4,
        '五'  => 5,
        '六'  => 6,
        '七'  => 7,
        '八'  => 8,
        '九'  => 9,
        '十'  => 10,
        '十一' => 11,
        '十二' => 12,
    ];
    private $datetimeStr;


    /**
     * DateParser constructor.
     * @param $datetimeStr
     */
    public function __construct($datetimeStr)
    {
        $this->datetimeStr = $datetimeStr;
    }


    public function toCarbon(): Carbon
    {
        // 2019-07-02 10:11 | 2019\07\02 | 2019/07/02 | 2019 07 02
        if ($this->isPatternFor(self::DATE_PATTERNS['FORMAL_DATE_PATTERN'])) {
            return $this->handleForFormalDatetime($this->datetimeStr);
        }

        //  禮拜六 \ 星期五 \ 下禮拜五 \ 下星期一 \ 下下星期一 + 早上|上午|中午|下午|晚上n點(n分) */
        if ($this->isPatternFor(self::DATE_PATTERNS['WEEKDAY_ALIAS_PATTERN'])) {
            return $this->handleForWeekdayAliasDatetime($this->datetimeStr);
        }

        // 今天 \ 明天 \ 後天 早上|上午|中午|下午|晚上n點(n分) */
        if ($this->isPatternFor(self::DATE_PATTERNS['CROSS_DAYS_ALIAS_PATTERN'])) {
            return $this->handleForCrossDaysAliasDatetime($this->datetimeStr);
        }

        /* 早上|上午|中午|下午|晚上n點(n分) */
        if ($this->isPatternFor(self::DATE_PATTERNS['TIME_ALIAS_PATTERN'])) {
            return $this->handleForTimeAliasDatetime($this->datetimeStr);
        }
    }


    public function validTimeInThePast($targetTime): bool
    {
        try {
            \Log::info("targetTime(validTimeInThePast) => " . print_r($targetTime, true));
            if ($targetTime->lessThan(Carbon::now('Asia/Taipei'))) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            \Log::error(__METHOD__ . " => " . $e);
            return false;
        }
    }


    public function getDelayTime(Carbon $targetTime): int
    {
        return $targetTime->diffInSeconds(Carbon::now('Asia/Taipei'));
    }


    private function isNeedToPlus12($timeInterval): bool
    {
        return in_array($timeInterval, ['下午', '晚上']);
    }


    private function timeFormatParse($time): string
    {
        $time = strtr(trim($time), self::CHINESE_NUMBER_MAP);
        \Log::info("time => {$time}");

        // check 5點
        $pattern = '/([0-1]*[0-9]+)點$/';
        if (preg_match($pattern, $time) == 1) {
            return preg_replace($pattern, "$1:00", $time);
        }

        // check 5點半
        $pattern = '/([0-1]*[0-9]+)點半$/';
        if (preg_match($pattern, $time) == 1) {
            return preg_replace($pattern, "$1:30", $time);
        }

        // check 5點30分
        $pattern = '/([0-1]*[0-9]+)點([0-1]*[0-9]+)分$/';
        if (preg_match($pattern, $time) == 1) {
            return preg_replace($pattern, "$1:$2", $time);
        }

        // check 5：30
        $pattern = '/([0-1]*[0-9]+)：([0-1]*[0-9]+)/';
        if (preg_match($pattern, $time) == 1) {
            return preg_replace($pattern, "$1:$2", $time);
        }
    }


    private function createTargetTime(string $dateTime, bool $isNeedPlus12Hours = false)
    {
        $targetTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime, 'Asia/Taipei');
        return $isNeedPlus12Hours ? $targetTime->addHours(12) : $targetTime;
    }


    private function getAddDaysCountFromCrossDay(string $crossDay): int
    {
        switch ($crossDay) {
            case '今天':
                return 0;
            case '明天':
                return 1;
            case '後天':
                return 2;
            default:
                return 0;
        }
    }


    private function dateTimeFormatParse($date, $dateAlias, $time): array
    {
        $isNeedPlus12 = $this->isNeedToPlus12($dateAlias);
        $time = $this->timeFormatParse($time);

        return ["{$date} {$time}", $isNeedPlus12];
    }


    private function getAddDaysCountByWeekday(int $targetDay): int
    {
        // get current day's weekday
        $nowDay = Carbon::now('Asia/Taipei')->dayOfWeek;

        // Today : Mon. =>  Target : Fri.
        return $nowDay <= $targetDay ? $targetDay - $nowDay : -1;
    }


    /**
     * @param $datetimeStr
     * @return Carbon
     */
    private function handleForFormalDatetime($datetimeStr): Carbon
    {
        $payload = explode(
            ',',
            preg_replace(
                self::DATE_PATTERNS['FORMAL_DATE_PATTERN'],
                "$1-$3-$5,$6",
                $datetimeStr
            )
        );

        $date = $payload[0];
        $time = $payload[1];

        return $this->createTargetTimeWithDateStrAndTime($time, $date);
    }


    /**
     * @param $datetimeStr
     * @return Carbon
     */
    private function handleForTimeAliasDatetime($datetimeStr): Carbon
    {
        $todayStr = Carbon::now('Asia/Taipei')->toDateString();

        $dateData = $this->parseTime($datetimeStr, $todayStr);

        return $this->createTargetTime($dateData[0], $dateData[1]);
    }


    private function isPatternFor($datePattern)
    {
        return preg_match($datePattern, $this->datetimeStr) === 1;
    }


    /**
     * @param        $datetimeStr
     * @param string $dateStr
     * @return array
     */
    private function parseTime($datetimeStr, string $dateStr): array
    {
        $times = explode(
            ',',
            preg_replace(
                self::DATE_PATTERNS['TIME_ALIAS_PATTERN'],
                "$1,$2",
                $datetimeStr
            )
        );

        return $this->dateTimeFormatParse($dateStr, $times[0], $times[1]);
    }


    /**
     * @param $datetimeStr
     * @return Carbon
     * @throws Exception
     */
    private function handleForWeekdayAliasDatetime($datetimeStr)
    {
        $payload = explode(
            ',',
            preg_replace(
                self::DATE_PATTERNS['WEEKDAY_ALIAS_PATTERN'],
                "$1,$4,$5",
                $datetimeStr
            )
        );

        /* 下 \ 下下 */
        $nextWeekStr = $payload[0];
        /* 星期n|禮拜n */
        $weekday = (int)strtr($payload[1], self::CHINESE_NUMBER_MAP);
        /* 下午六點半|晚上九點 */
        $time = $payload[2];

        $nextWeekCount = mb_strlen($nextWeekStr);
        $addDays = $this->getAddDaysCountByWeekday($weekday);
        $addDays += $nextWeekCount * 7;

        if ($addDays < 0) {
            throw new \Exception("Add Day is less than 0");
        }

        $targetDate = Carbon::now('Asia/Taipei')->addDays($addDays);

        return $this->createTargetTimeWithDateStrAndTime($time, $targetDate->toDateString());
    }


    private function handleForCrossDaysAliasDatetime($datetimeStr)
    {
        $payload = explode(
            ',',
            preg_replace(
                self::DATE_PATTERNS['CROSS_DAYS_ALIAS_PATTERN'],
                "$1,$2",
                $datetimeStr
            )
        );

        /* 今天 / 明天 / 後天 */
        $crossDay = $payload[0];
        /* 下午六點半|晚上九點 */
        $time = $payload[1];

        $addDays = $this->getAddDaysCountFromCrossDay($crossDay);
        $targetDate = Carbon::now('Asia/Taipei')->addDays($addDays);

        return $this->createTargetTimeWithDateStrAndTime($time, $targetDate->toDateString());
    }


    /**
     * @param string $time
     * @param string $targetDateStr
     * @return Carbon
     */
    private function createTargetTimeWithDateStrAndTime(string $time, string $targetDateStr): Carbon
    {
        $dateData = $this->parseTime($time, $targetDateStr);
        return $this->createTargetTime($dateData[0], $dateData[1]);
    }
}
