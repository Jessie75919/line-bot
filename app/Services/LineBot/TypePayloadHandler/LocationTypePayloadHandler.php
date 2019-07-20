<?php


namespace App\Services\LineBot\TypePayloadHandler;

use App\Services\LineBot\ActionHandler\LineBotCommandHelper;
use App\Services\LineBot\ActionHandler\LineBotActionFoodNearbySearcher;

class LocationTypePayloadHandler implements TypePayloadHandlerInterface
{
    const NEARBY_SEARCH = 'nearby_search';
    private $memory;
    private $rawPayload;
    private $payload;
    /** * @var string */
    private $purpose;


    /**
     * TextPurposeChecker constructor.
     * @param $memory
     */
    public function __construct($memory)
    {
        $this->memory = $memory;
    }


    public function checkPurpose($payload)
    {
        $this->rawPayload = $payload;
        $this->purpose = self::NEARBY_SEARCH;
        return $this;
    }


    public function preparePayload()
    {
        $this->payload = [
            'channelId' => $this->memory->channel_id,
            'purpose'   => $this->purpose,
            'message'   => [
                'origin'    => $this->rawPayload,
                'address'   => $this->rawPayload['address'],
                'latitude'  => $this->rawPayload['latitude'],
                'longitude' => $this->rawPayload['longitude'],
            ]
        ];
        return $this;
    }


    public function dispatch()
    {
        switch ($this->purpose) {
            case self::NEARBY_SEARCH:
                return new LineBotActionFoodNearbySearcher($this->payload);
                break;

            default:
                $this->payload['purpose'] = 'help';
                return new LineBotCommandHelper($this->payload);
        }
    }
}