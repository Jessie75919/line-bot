<?php

namespace App\Services\LineBot\ActionHandler;

use App\Services\LineBot\LineBotMessageResponser;

class LineBotCommandHelper implements LineBotActionHandlerInterface
{

    protected $payload;


    public function __construct($payload)
    {
        $this->payload = $payload;
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
