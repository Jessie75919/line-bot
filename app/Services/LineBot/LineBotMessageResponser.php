<?php

namespace App\Services\LineBot;

use App\Models\Message;

class LineBotMessageResponser
{
    const GENERAL_RESPONSE = '好喔～好喔～';
    const ERROR_MESSAGE = 'Oh!oh!好像那裡有問題了 QQ';
    private $channelId;
    private $purpose;
    private $content;

    public function __construct($channelId, $purpose, $content = null)
    {
        $this->channelId = $channelId;
        $this->purpose = $purpose;
        $this->content = $content;
    }

    /**
     * @param $userMsg
     * @return string
     */
    public function keywordReply($userMsg): ?string
    {
        $resp = Message::where('keyword', $userMsg)
            ->where('channel_id', $this->channelId)->get();

        return count($resp) != 0 ? $resp->random()->message : null;
    }

    /**
     * @return string
     */
    public function responseToUser(): ?string
    {
        switch ($this->purpose) {
            case 'response':
                return $this->content;
            case 'talk':
                return $this->keywordReply($this->content);
        }
    }

    /**
     * @param  null  $content
     * @return LineBotMessageResponser
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
