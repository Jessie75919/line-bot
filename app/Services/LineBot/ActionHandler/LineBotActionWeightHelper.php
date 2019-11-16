<?php

namespace App\Services\LineBot\ActionHandler;

class LineBotActionWeightHelper extends LineBotActionHandler
{
    private $payload;

    public function preparePayload($rawPayload)
    {
        $breakdownMessage = $this->breakdownMessage($rawPayload);

        \Log::info(__METHOD__.' => '.print_r($breakdownMessage, true));
        \Log::info(__METHOD__.' => '.print_r(json_decode($breakdownMessage[1]), true));

        $this->payload = [
            'channelId' => $this->channelId,
            'purpose' => $this->purpose,
            'message' => [
                'origin' => $rawPayload,
                'key' => count($breakdownMessage) > 0 ? $breakdownMessage[1] : null,
                'value' => count($breakdownMessage) === 3 ? $breakdownMessage[2] : null,
            ],
        ];

        return $this;
    }

    public function handle()
    {
        \Log::info(__METHOD__." => ".'go');
    }
}