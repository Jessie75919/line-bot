<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/26星期一
 * Time: 下午9:22
 */

namespace App\Services\LineBot;

use App\Models\Memory;
use App\Models\Message;

class LineBotMessageResponser
{
    const GENERAL_RESPONSE = '好喔～好喔～';
    private $channelId;
    private $purpose;
    private $content;


    public function __construct($channelId, $purpose, $content = null)
    {
        $this->channelId = $channelId;
        $this->purpose   = $purpose;
        $this->content   = $content;
    }


    /**
     * @param $userMsg
     * @return string
     */
    public function keywordReply($userMsg): string
    {
        \Log::info('userMsg = '.$userMsg);
        $resp = Message::where('keyword', strtolower($userMsg))->where('channel_id', $this->channelId)->get();

        return count($resp) != 0 ? $resp->random()->message : '';
    }


    /**
     * @param  bool  $shutUp
     */
    public function setTalk(bool $shutUp): void
    {
        Memory::where('channel_id', $this->channelId)->update(['is_talk' => $shutUp]);
    }


    /**
     * @return string
     */
    public function responseToUser(): string
    {
        switch ($this->purpose) {
            case 'response':
                return $this->content;
            case 'talk':
                return $this->keywordReply($this->content);
            case 'speak':
                $this->setTalk(1);
                return self::GENERAL_RESPONSE;
            case 'shutUp':
                $this->setTalk(0);
                return self::GENERAL_RESPONSE;
            case 'state':
                $isTalk    = Memory::where('channel_id', $this->channelId)->first()->is_talk;
                $stateText = "channel_id : \n [ {$this->channelId } \n";

                return ! $isTalk ? $stateText." 目前處於 \n [ 閉嘴狀態 ]" : $stateText." 目前處於 \n [可以講話狀態 ]";
        }
    }


    /**
     * @param  mixed  $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }
}
