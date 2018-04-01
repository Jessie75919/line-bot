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

    private $channelId;
    private $purpose;
    private $content;
    const GENERAL_RESPONSE = '好喔～好喔～';


    public function __construct($channelId, $purpose , $content=null)
    {
        $this->channelId = $channelId;
        $this->purpose   = $purpose;
        $this->content   = $content;

        $this->responsePurpose();

        //Todo:: need check purpose and response
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


    public function responsePurpose()
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
        }
    }


    private function response($responseText)
    {
        return $responseText;
    }


}