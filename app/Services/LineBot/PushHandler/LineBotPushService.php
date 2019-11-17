<?php

namespace App\Services\LineBot\PushHandler;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Response;

/** This is a app entry point
 * Class LineBotService
 * @package App\Services
 */
class LineBotPushService
{
    /** @var LINEBot */
    private $lineBot;
    /** @var string */
    private $lineUserId;

    public function __construct()
    {
        $this->lineBot = app(LINEBot::class);
    }

    /**
     * @param                               $channelId
     * @param  TemplateMessageBuilder|string  $content
     * @return Response
     */
    public function pushMessage($channelId, $content): Response
    {
        if (is_string($content)) {
            $content = new TextMessageBuilder($content);
        }
        return $this->lineBot->pushMessage($channelId, $content);
    }

    /**
     * @param  string  $lineUserId
     */
    public function setLineUserId(string $lineUserId)
    {
        $this->lineUserId = $lineUserId;
    }

    /**
     * @return LINEBot
     */
    public function getLineBot(): LINEBot
    {
        return $this->lineBot;
    }
}
