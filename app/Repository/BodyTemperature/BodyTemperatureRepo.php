<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午12:14
 */

namespace App\Repository\BodyTemperature;


use App\Models\BodyTemperature\BodyTemperature;
use App\Utilities\DateTools;
use function explode;

class BodyTemperatureRepo
{
    public static function getRangeData($begin, $end, $userId)
    {
        $sortData =
            BodyTemperature::where('user_id', $userId)
                           ->orderBy('month', 'asc')
                           ->orderBy('day', 'asc')->get();

        $year = DateTools::thisYear();

        return $sortData->filter(function ($item) use ($begin, $end, $year) {
            $date = DateTools::createCarbonByDateStr("{$year}-{$item->month}-{$item->day}");
            return $date->between($begin, $end);
        });
    }


    public static function getModelByDate($date, $userId, $delimiter = "-")
    {

        $dateArr = explode($delimiter, $date);

        return BodyTemperature::where([
            ['month', $dateArr[1]],
            ['day', $dateArr[2]],
            ['user_id', $userId],
        ])->first();
    }


    public static function getModelByMonthDay($month, $day, $userId)
    {
        return BodyTemperature::where([
            ['month', $month],
            ['day', $day],
            ['user_id', $userId],
        ])->first();
    }


    public static function fillEmptyDataBetween($begin, $end, $userId)
    {
        $dates = [$begin, $end];

        foreach ($dates as $date) {

            $datePreviousTemperature = 36;

            foreach (range(1, $date->daysInMonth) as $item) {
                $dateAlready = self::getModelByMonthDay($date->month, $item, $userId);

                if (!$dateAlready) {
                    BodyTemperature::create([
                        'user_id'     => $userId,
                        'month'       => $date->month,
                        'day'         => $item,
                        'temperature' => $datePreviousTemperature
                    ]);
                    continue;
                }

                $datePreviousTemperature = $dateAlready->temperature;
            }
        }
    }


    private static function getIdByDate(string $date, $userId)
    {
        return self::getModelByDate($date, $userId)->id;
    }
}