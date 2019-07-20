<?php


namespace App\Services\LineBot\ActionHandler;

class LineBotActionFoodNearbySearcher implements LineBotActionHandlerInterface
{
    private $payload;


    /**
     * LineBotActionFoodNearbySearcher constructor.
     * @param $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }


    public function handle()
    {
        dd($this->payload);
    }
}