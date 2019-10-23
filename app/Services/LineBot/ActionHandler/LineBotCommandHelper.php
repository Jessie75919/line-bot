<?php

namespace App\Services\LineBot\ActionHandler;

use App\Services\LineBot\LineBotMessageResponser;

class LineBotCommandHelper extends LineBotActionHandler
{

    protected $payload;

    public function preparePayload($rawPayload)
    {
        $breakdownMessage = $this->breakdownMessage($rawPayload);

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
        return (new LineBotMessageResponser(
            $this->payload['channelId'],
            $this->payload['purpose'],
            $this->payload['message']['origin']
        ))->responseToUser();
    }
}
