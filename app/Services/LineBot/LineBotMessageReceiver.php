<?php

namespace App\Services\LineBot;

use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineBot\TypePayloadHandler\LocationTypePayloadHandler;
use App\Services\LineBot\TypePayloadHandler\TextTypePayloadHandler;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class LineBotMessageReceiver
{
    public $purpose;
    private $userDataType;
    private $memory;

    public function getHandler(BaseEvent $messageEvent)
    {
        return $this->createMemory($messageEvent)
            ->dispatchByPayloadType($messageEvent);
    }

    public function dispatchByPayloadType(BaseEvent $messageEvent): LineBotActionHandler
    {
        if ($messageEvent instanceof TextMessage) {
            $this->userDataType = 'text';
            return (new TextTypePayloadHandler($this->memory))
                ->checkPurpose($messageEvent)
                ->dispatch();
        }

        if ($messageEvent instanceof LocationMessage) {
            $this->userDataType = 'location';
            return (new LocationTypePayloadHandler($this->memory))
                ->checkPurpose($messageEvent)
                ->preparePayload($messageEvent)
                ->dispatch();
        }
    }

    /**
     * @return string
     */
    public function getUserDataType(): string
    {
        return $this->userDataType;
    }

    /** for the first time user which not has record in DB
     * @param $messageEvent
     * @return LineBotMessageReceiver
     */
    private function createMemory(TextMessage $messageEvent)
    {
        $channelId = $messageEvent->getEventSourceId();

        $this->memory = Memory::firstOrCreate(['channel_id' => $channelId], ['is_talk' => 1]);
        return $this;
    }
}
