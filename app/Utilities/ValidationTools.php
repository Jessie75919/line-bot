<?php


namespace App\Utilities;


class ValidationTools
{

    public static function isExisted($model, $col, $value)
    {
        return $model::where($col, $value)->first();
    }
}