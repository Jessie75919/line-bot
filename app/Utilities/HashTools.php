<?php
namespace App\Utilities;

class HashTools
{

    public static function generateHash()
    {
        return md5(uniqid(rand(), true));
    }

}