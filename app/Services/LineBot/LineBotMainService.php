<?php

namespace App\Services\LineBot;

use App\Services\LineBot\PushHandler\LineBotPushService;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;

class LineBotMainService
{
    /** @var LineBotMessageReceiver */
    private $lineBotReceiver;
    /* @var LINEBot */
    private $lineBot;

    /**
     * LineBotMainService constructor.
     * @param  LINEBot  $lineBot
     * @param  LineBotMessageReceiver  $lineBotReceiver
     */
    public function __construct(
        LINEBot $lineBot,
        LineBotMessageReceiver $lineBotReceiver
    ) {
        $this->lineBot = $lineBot;
        $this->lineBotReceiver = $lineBotReceiver;
    }

    /**
     * @param $body
     * @param $signature
     * @return LINEBot\Event\BaseEvent[]
     */
    public function parseEventRequest($body, $signature)
    {
        return $this->lineBot->parseEventRequest($body, $signature);
    }

    public function handle(BaseEvent $messageEvent)
    {
        if (! $handler = $this->lineBotReceiver->getHandler($messageEvent)) {
            return null;
        }

        if (! $responsePayload = $handler->handle()) {
            return null;
        }

        $replyToken = $messageEvent->getReplyToken();
        $dataType = $this->lineBotReceiver->getUserDataType();

        if ($dataType === 'text') {
            return $this->lineBot->replyText($replyToken, $responsePayload);
        }

        if ($dataType === 'location') {
            $this->lineBot->replyMessage(
                $replyToken,
                (new LineBotPushService())->buildTemplateMessageBuilder($responsePayload)
            );
        }
    }
}
