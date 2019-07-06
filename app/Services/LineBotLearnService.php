<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/31星期六
 * Time: 下午6:11
 */

namespace App\Services;



use App\Models\Message;

class LineBotLearnService
{
    private $channelId;
    private $learnContent;

    /**
     * LineBotLearnService constructor.
     * @param string $channelId
     * @param array $learnContent
     */
    public function __construct($channelId, $learnContent)
    {
        $this->channelId    = $channelId;
        $this->learnContent = $learnContent;
    }


    /**
     * @return bool
     * @internal param $key
     * @internal param $message
     */
    public function learnCommand():bool
    {
        $key     = trim($this->learnContent[0]);
        $message = trim($this->learnContent[1]);

        \Log::info('key = '. $key);
        \Log::info('message = '.$message);

        if(strlen($key) <= 0 && strlen($message) <= 0) {
            return false;
        }

        Message::create([
            'keyword'    => $key,
            'message'    => $message,
            'channel_id' => $this->channelId
        ]);

        return true;
    }
}