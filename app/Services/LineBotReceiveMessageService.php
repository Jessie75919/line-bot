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

        /** @var  array */
        $data = $package['events']['0'];

        /** @var  string */
        $type = $data['type'];

        /** @var array */
        $source = $data['source'];

        /** @var  array */
        $message = $data['message'];

        $this->replyToken = $data['replyToken'];

        switch($type) {
            case 'message':
                // deals with source
                switch($source['type']) {
                    case 'user':
                        $this->channelId = $source['userId'];
                        break;
                    case 'group':
                        $this->channelId = $source['groupId'];
                        break;
                    case 'room':
                        $this->channelId = $source['roomId'];
                        break;
                }

                // deals with message
                switch($message['type']) {
                    case 'text':
                        $this->userMessage = $message['text'];
                        break;
                }
                break;
        }

    }


    /**
     * @return string
     */
    public function getReplyToken():string
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

}