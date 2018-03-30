<?php


namespace App\Services;


class LineBotReceiveMessageService
{
    public $replyToken;
    public $userMessage;
    public $channelId;


    /**
     * LineBotReceiveMessageService constructor.
     * @param $package
     * @internal param $replyToken
     * @internal param $channelId
     */
    public function __construct($package)
    {

         \Log::info('package = '. print_r($package , true));

        $this->replyToken  = $package['events']['0']['replyToken'];
        $this->channelId   = $package['events']['0']['source']['userId'];
    }


    /**
     * @return string
     */
    public function getReplyToken()
    {
        return $this->replyToken;
    }


    /**
     * @return string
     */
    public function getUserMessage():string
    {
       return $this->userMessage;
    }


    /**
     * @return string
     */
    public function getChannelId():string
    {
        return $this->channelId;
    }


    /**
     * @param $package
     */
    public function setUserMessage($package):void
    {
        if($package['events']['0']['message']){
            $userMsg = $package['events']['0']['message'];
            if($userMsg['type'] === 'text' ) {
                $this->userMessage = $userMsg['text'];
            }
        }
    }


}