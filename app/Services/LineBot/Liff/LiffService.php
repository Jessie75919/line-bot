<?php

namespace app\Services\LineBot\Liff;

class LiffService
{
    public static function parsePageToken(string $linkUrl)
    {
        return preg_replace('/line:\/\/app\/(.*)/m', '$1', $linkUrl);
    }
}