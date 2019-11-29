<?php

namespace App\Services\LineBot\TypePayloadHandler;

use App\Services\LineBot\ActionHandler\LineBotActionFoodNearbySearcher;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;

class LocationTypePayloadHandler implements TypePayloadHandlerInterface
{
    const NEARBY_SEARCH = 'nearby_search';
    private $memory;
    private $payload;
    private $purpose;

    /**
     * TextPurposeChecker constructor.
     * @param $memory
     */
    public function __construct($memory)
    {
        $this->memory = $memory;
    }

    public function route($messageEvent)
    {
        $this->purpose = self::NEARBY_SEARCH;
        return $this;
    }

    public function preparePayload(LocationMessage $messageEvent)
    {
        $this->payload = [
            'channelId' => $this->memory->channel_id,
            'purpose' => $this->purpose,
            'origin' => $messageEvent,
            'address' => $messageEvent->getAddress(),
            'latitude' => $messageEvent->getLatitude(),
            'longitude' => $messageEvent->getLongitude(),
        ];
        return $this;
    }

    public function dispatch()
    {
        switch ($this->purpose) {
            case self::NEARBY_SEARCH:
                return new LineBotActionFoodNearbySearcher($this->payload);
                break;
        }
    }
}