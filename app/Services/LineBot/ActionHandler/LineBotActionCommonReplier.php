<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot\ActionHandler;

use App\Services\LineBot\LineBotMessageResponser;

class LineBotActionCommonReplier extends LineBotActionHandler
{
    private $payload;

    public function preparePayload($rawPayload)
    {
        $this->payload = [
            'channelId' => $this->channelId,
            'purpose' => $this->purpose,
            'message' => [
                'origin' => $rawPayload,
                'key' => null,
                'value' => null,
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
