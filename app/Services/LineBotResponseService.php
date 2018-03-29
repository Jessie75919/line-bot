<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/26星期一
 * Time: 下午9:22
 */

namespace App\Services;


use App\Memory;
use App\Message;
use const false;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use const true;

class LineBotResponseService
{

    private $shutUp;
    private $channelId;


    public function __construct($channelId)
    {
        $this->shutUp    = Memory::where('channel_id', $channelId)->first()->is_talk;
        $this->channelId = $channelId;
    }


    /**
     * @param $userMsg
     * @return string
     */
    public function keywordReply($userMsg)
    {
        \Log::info('userMsg = '.$userMsg);
        $resp = Message::where('keyword',strtolower($userMsg))->get();

        return count($resp) != 0
            ? $resp->random()->message
            : '不要再講幹話好嗎！！';
    }


    /**
     * @param string $learnWord
     * @return bool
     */
    public function isLearningCommand($learnWord):bool
    {
        return trim($learnWord) == '學' ? true : false;
    }


    /**
     * @param $key
     * @param $message
     * @return bool
     */
    public function learnCommand($key, $message):bool
    {
        \Log::info('key = '. $key);
        \Log::info('message = '.$message);

        if(strlen($key) <= 0 && strlen($message) <= 0) {
            return false;
        }

        $key     = trim($key);
        $message = trim($message);

        Message::create([
            'keyword'    => $key,
            'message'    => $message,
            'channel_id' => $this->channelId
        ]);

        return true;
    }


    /**
     * @param bool $shutUp
     */
    public function setShutUp(bool $shutUp):void
    {
       Memory::where('channel_id', $this->channelId)->update(['is_talk' => $shutUp]);
    }


    /**
     * @return bool
     */
    public function isShutUp():bool
    {
        return  $this->shutUp ;
    }


    /**
     * @param string $keyword
     * @return bool
     */
    public function isNeedShutUp(string $keyword):bool
    {
        \Log::info('isNeedShutUp = '.$keyword);
        $result = strpos($keyword, 'ChuC 安靜');
        \Log::info('result = '.$result);
        return $result === false ? false : true;
    }


    public function isNeedTalk(string $keyword):bool
    {
        return strpos($keyword, 'ChuC 講話') === false ? false : true;
    }
}