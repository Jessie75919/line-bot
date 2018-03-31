<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/31星期六
 * Time: 下午6:11
 */

namespace App\Services;


use App\Message;
use function count;
use function explode;

class LineBotLearnService
{
    private $channelId;


    /**
     * LineBotLearnService constructor.
     * @param $channelId
     */
    public function __construct($channelId)
    {
        $this->channelId = $channelId;
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
     * @param $userMsg
     * @return array
     */
    public function learning($userMsg):array
    {
        $strArr = explode(';', $userMsg) ;

        return count($strArr) == 3
            ? $strArr
            : explode('；', $userMsg);

    }


}