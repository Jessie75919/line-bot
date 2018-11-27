<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/6/8星期五
 * Time: 下午3:32
 */

namespace App\Utilities;


use Carbon\Carbon;

class DateTools
{
    const ASIA_TAIPEI = 'Asia/Taipei';


    public static function today(): Carbon
    {
        return Carbon::now(self::ASIA_TAIPEI);
    }


    public static function thisYear(): int
    {
        return Carbon::now(self::ASIA_TAIPEI)->year;
    }


    public static function thisLunarYear(): int
    {
        return self::thisYear() - 1911;
    }


    public static function thisMonth(): int
    {
        return Carbon::now(self::ASIA_TAIPEI)->month;
    }


    public static function thisDay(): int
    {
        return Carbon::now(self::ASIA_TAIPEI)->day;
    }


    public static function createCarbonByDateStr($date, $delimiter = '-'): Carbon
    {
        $dateArr = explode($delimiter, $date);
        return Carbon::createFromDate(
            $dateArr[0],
            $dateArr[1],
            $dateArr[2],
            self::ASIA_TAIPEI
        );
    }


    public static function toChineseYear($westernYear)
    {
        return $westernYear - 1911;
    }


    public static function toWesternYear($ChineseYear)
    {
        return $ChineseYear + 1911;
    }


    /**
     * @param $dateStr // ex: 1070919
     * @return array
     * @throws \Exception
     */
    public static function parseStrToDateArr(string $dateStr)
    {
        self::checkStrLen($dateStr, 7);
        $lunarYear = (int)substr($dateStr, 0, 3);
        return [
            'year'  => (string)self::toWesternYear($lunarYear),
            'month' => substr($dateStr, 3, 2),
            'day'   => substr($dateStr, 5, 2)
        ];
    }


    /**
     * @param string $timeStr // ex: 161217
     * @return array
     */
    public static function parseStrToTimeArr(string $timeStr)
    {
        self::checkStrLen($timeStr, 6);
        return [
            'hour'   => substr($timeStr, 0, 2),
            'minute' => substr($timeStr, 2, 2),
            'second' => substr($timeStr, 4, 2),
        ];
    }


    /**
     * @param array      $dateArr
     * @param array|null $timeArr
     * @return Carbon
     */
    public static function createCarbonFromDateTime(array $dateArr, array $timeArr = null): Carbon
    {
        if ( ! $timeArr) {
            $timeArr = ['hour' => 0, 'minute' => 0, 'second' => 0];
        }
        return Carbon::create($dateArr['year'], $dateArr['month'], $dateArr['day'],
            $timeArr['hour'], $timeArr['minute'], $timeArr['second'], self::ASIA_TAIPEI);
    }


    public static function toChineseDate(string $date, $delimiter , $afterDelimiter = ".")
    {
        $dateArr = explode($delimiter, $date);
        return sprintf('%s%s%s%s%s',
            $chineseYear = self::toChineseYear($dateArr[0]),
            $afterDelimiter,
            $dateArr[1],
            $afterDelimiter,
            $dateArr[2]
        );
    }


    public static function nextWeekday(Carbon $today, int $plusDays = 1)
    {
        for ($i = 1; $i <= $plusDays; $i++) {
            $today->nextWeekday();
        }

        return $today;
    }


    public static function getChineseDateStr()
    {
        $lunarYear = self::thisLunarYear();
        $month     = self::thisMonth();
        $day       = self::thisDay();
        return "{$lunarYear}-{$month}-{$day}";
    }


    /**
     * @param $dateStr
     * @param $length
     * @throws \Exception
     */
    private static function checkStrLen($dateStr, $length)
    {
        if (strlen($dateStr) !== $length) {
            throw new \Exception("Date String length is not equal to {$length}");
        }
    }

}