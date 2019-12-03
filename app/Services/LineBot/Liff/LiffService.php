<?php

namespace App\Services\LineBot\Liff;

class LiffService
{
    public function parsePageToken(string $linkUrl): string
    {
        return preg_replace('/line:\/\/app\/(.*)/m', '$1', $linkUrl);
    }

    public function parseToHttpsUrl(string $linkUrl): string
    {
        return 'https://liff.line.me/'.$this->parsePageToken($linkUrl);
    }
}