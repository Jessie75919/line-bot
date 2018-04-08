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

class LineBotResponseService
{

    private $channelId;
    private $purpose;
    private $content;
    const GENERAL_RESPONSE = '好喔～好喔～';


    public function __construct($channelId, $purpose, $content = null)
    {
        $this->channelId = $channelId;
        $this->purpose   = $purpose;
        $this->content   = $content;

        $this->responseToUser();
    }


    /**
     * @param $userMsg
     * @return string
     */
    public function keywordReply($userMsg): string
    {
        \Log::info('userMsg = ' . $userMsg);
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
    public function setTalk(bool $shutUp): void
    {
        Memory::where('channel_id', $this->channelId)->update(['is_talk' => $shutUp]);
    }


    /**
     * @return string
     */
    public function responseToUser(): string
    {
        switch($this->purpose) {
            case 'response' :
                return $this->response($this->content);
                break;
            case 'talk':
                $responseText = $this->keywordReply($this->content);
                return $this->response($responseText);
                break;
            case 'speak':
                $this->setTalk(1);
                return $this->response(self::GENERAL_RESPONSE);
                break;
            case 'shutUp':
                $this->setTalk(0);
                return $this->response(self::GENERAL_RESPONSE);
                break;
            case 'state':
                $isTalk = Memory::where('channel_id', $this->channelId)->first()->is_talk;
                $stateText = "我的 channel_id 為 [ {$this->channelId }, ";

                if($isTalk == 0){
                    $stateText .= "目前處於 [ 閉嘴狀態 ]";
                }else {
                    $stateText .= " 目前處於 [可以講話狀態 ]";
                }
                return $this->response($stateText);
                break;
        }
    }


    /**
     * @param string $responseText
     * @return mixed
     */
    private function response($responseText): string
    {
        return $responseText;
    }
}