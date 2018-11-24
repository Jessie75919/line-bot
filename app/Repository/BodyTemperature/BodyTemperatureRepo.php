<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午12:14
 */

namespace App\Repository\BodyTemperature;


use App\Models\BodyTemperature\BodyTemperature;
use function explode;

class BodyTemperatureRepo
{
    public static function getRangeData($begin, $end)
    {
        $beginId = self::getIdByDate($begin);
        $endId   = self::getIdByDate($end);

        return BodyTemperature::whereBetween('id', [$beginId, $endId])->get();
    }


    public static function getModelByDate($date)
    {
        $dateArr = explode('-', $date);

        return BodyTemperature::where([
            ['month', $dateArr[1]],
            ['day', $dateArr[2]],
        ])->first();
    }


    private static function getIdByDate(string $date)
    {
        return self::getModelByDate($date)->id;
    }
}