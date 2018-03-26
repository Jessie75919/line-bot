<?php


namespace App\Services;


class LineBotReceiveMessageService
{

    /**
     * LineBotReceiveMessageService constructor.
     */
    public function __construct()
    {
    }


    /**
     * @param $package
     * @return string
     */
    public function getReplyToken($package)
    {
        return $package['events']['0']['replyToken'];
    }


    /**
     * @param $package
     * @return mixed | string
     */
    public function getUserMessage($package)
    {
        // only respond text type message
        if($package['events']['0']['message']){
            $userMsg = $package['events']['0']['message'];
            if($userMsg['type'] === 'text' ) {
                return $userMsg['text'];
            }
        }
    }
}