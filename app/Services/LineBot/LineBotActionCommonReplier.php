<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot;

class LineBotActionCommonReplier implements LineBotActionHandlerInterface
{
    private $payload;


    /**
     * LineBotCommonReplier constructor.
     * @param $payload
     */
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
