<?php

namespace App\Services\LineBot;

use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineBot\TypePayloadHandler\LocationTypePayloadHandler;
use App\Services\LineBot\TypePayloadHandler\TextTypePayloadHandler;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\PostbackEvent;

class LineBotMessageReceiver
{
    public $purpose;
    private $memory;

    public function getHandler(BaseEvent $messageEvent)
    {
        return $this->createMemory($messageEvent)
            ->dispatchByPayloadType($messageEvent);
    }

    public function dispatchByPayloadType(BaseEvent $messageEvent): LineBotActionHandler
    {
        if ($messageEvent instanceof TextMessage ||
            $messageEvent instanceof PostbackEvent
        ) {
            return (new TextTypePayloadHandler($this->memory))
                ->checkRoute($messageEvent)
                ->dispatch();
        }

        if ($messageEvent instanceof LocationMessage) {
            return (new LocationTypePayloadHandler($this->memory))
                ->checkRoute($messageEvent)
                ->preparePayload($messageEvent)
                ->dispatch();
        }
    }

    /** for the first time user which not has record in DB
     * @param $messageEvent
     * @return LineBotMessageReceiver
     * @throws \LINE\LINEBot\Exception\InvalidEventSourceException
     */
    private function createMemory(BaseEvent $messageEvent)
    {
        $channelId = $messageEvent->getEventSourceId();

        $this->memory = Memory::firstOrCreate(['channel_id' => $channelId], ['is_talk' => 1]);
        return $this;
    }
}
