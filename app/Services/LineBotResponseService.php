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
use function in_array;
use function substr;
use const true;

class LineBotResponseService
{

    private $isTalk;
    private $channelId;


    public function __construct($channelId)
    {
        $this->isTalk    = Memory::where('channel_id', $channelId)->first()->is_talk;
        $this->channelId = $channelId;
        \Log::info('is_talk = '. $this->isTalk);
    }


    /**
     * @param $userMsg
     * @return string
     */
    public function keywordReply($userMsg)
    {
        \Log::info('userMsg = '.$userMsg);
        $resp = Message::where('keyword', strtolower($userMsg))
                       ->where('channel_id', $this->channelId)
                       ->get();

        return count($resp) != 0
            ? $resp->random()->message
            : '';
    }




    /**
     * @param bool $shutUp
     */
    public function setTalk(bool $shutUp):void
    {
       Memory::where('channel_id', $this->channelId)->update(['is_talk' => $shutUp]);
    }


    /**
     * @return bool
     */
    public function isTalk():bool
    {
        return  $this->isTalk ;
    }


    /**
     * @param string $keyword
     * @param string $mode
     * @return bool
     */
    public function isNeed(string $keyword, string $mode):bool
    {
        $talkCmd   = ['講話', '說話', '開口'];
        $shutUpCmd = ['閉嘴', '安靜', '吵死了'];

        $cmd = substr($keyword, 3);
        \Log::info('cmd = '. $cmd);

        switch($mode){
            case 'talk':
                return in_array($cmd, $talkCmd);
                break;
            case 'shutUp':
                return in_array($cmd, $shutUpCmd);
                break;
        }
    }
}