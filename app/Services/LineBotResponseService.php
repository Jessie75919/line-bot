<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/26星期一
 * Time: 下午9:22
 */

namespace App\Services;


use App\Message;
use const false;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use const true;

class LineBotResponseService
{

    private $shutUp;


    public function __construct()
    {
        $this->shutUp = false;
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
    public function isLearningCommand($learnWord)
    {
        return trim($learnWord) == '學' ? true : false;
    }


    /**
     * @param $key
     * @param $message
     * @return bool
     */
    public function learnCommand($key, $message)
    {
        \Log::info('key = '. $key);
        \Log::info('message = '.$message);

        if(strlen($key) <= 0 && strlen($message) <= 0) {
            return false;
        }

        $key     = trim($key);
        $message = trim($message);

        Message::create([
            'keyword' => $key,
            'message' => $message
        ]);

        return true;
    }


    /**
     * @param bool $shutUp
     */
    public function setShutUp(bool $shutUp)
    {
        $this->shutUp = $shutUp;
    }


    /**
     * @return bool
     */
    public function isShutUp():bool
    {
        return $this->shutUp;
    }


    /**
     * @param string $keyword
     * @return bool
     */
    public function isNeedShutUp(string $keyword):bool
    {
        return strpos($keyword, 'ChuC 安靜') === false ? false : true;
    }


    public function isNeedTalk(string $keyword):bool
    {
        return strpos($keyword, 'ChuC 講話') === false ? false : true;
    }
}