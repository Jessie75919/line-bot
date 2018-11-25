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
    public static function getRangeData($begin, $end, $userId)
    {
        $beginId = self::getIdByDate($begin, $userId);
        $endId   = self::getIdByDate($end, $userId);

        return BodyTemperature::whereBetween('id', [$beginId, $endId])->get();
    }


    public static function getModelByDate($date, $userId)
    {
        $dateArr = explode('-', $date);

        return BodyTemperature::where([
            ['month', $dateArr[1]],
            ['day', $dateArr[2]],
            ['user_id', $userId],
        ])->first();
    }


    private static function getIdByDate(string $date, $userId)
    {
        return self::getModelByDate($date, $userId)->id;
    }
}