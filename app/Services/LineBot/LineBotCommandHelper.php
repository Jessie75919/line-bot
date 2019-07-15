<?php

namespace App\Services\LineBot;


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
