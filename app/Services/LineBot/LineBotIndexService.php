<?php

namespace App\Services\LineBot;

use App\Services\LineBot\Router\LineBotRouter;
use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;
use ReflectionException;

class LineBotIndexService
{
    /* @var LINEBot */
    private $lineBot;

    /**
     * LineBotMainService constructor.
     * @param  LINEBot  $lineBot
     */
    public function __construct(LINEBot $lineBot)
    {
        $this->lineBot = $lineBot;
    }

    /**
     * @param $body
     * @param $signature
     * @return LINEBot\Event\BaseEvent[]
     * @throws LINEBot\Exception\InvalidEventRequestException
     * @throws LINEBot\Exception\InvalidSignatureException
     */
    public function parseEventRequest($body, $signature)
    {
        return $this->lineBot->parseEventRequest($body, $signature);
    }

    public function handle(BaseEvent $messageEvent)
    {
        $controller = app(LineBotRouter::class, ['messageEvent' => $messageEvent])
            ->getController();

        $message = $controller->handle();

        \Log::info(__METHOD__."[".__LINE__."] => ".$message);

        try {
            return $this->lineBot->replyText($messageEvent->getReplyToken(), $message);
        } catch (ReflectionException $e) {
            \Log::error(__METHOD__." => ".$e);
        }
    }
}
