<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/31星期六
 * Time: 下午6:11
 */

namespace App\Services\LineBot\ActionHandler;

use App\Models\Message;
use App\Services\LineBot\LineBotMessageResponser;

class LineBotActionLearner extends LineBotActionHandler
{
    private $payload;

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
        $key = $this->payload['message']['key'];
        $message = $this->payload['message']['value'];

        \Log::info('key = '.$key);
        \Log::info('message = '.$message);

        if (strlen($key) <= 0 && strlen($message) <= 0) {
            return false;
        }

        try {
            Message::create(
                [
                    'keyword' => $key,
                    'message' => $message,
                    'channel_id' => $this->payload['channelId'],
                ]
            );

            return (new LineBotMessageResponser(
                $this->payload['channelId'],
                'response',
                LineBotMessageResponser::GENERAL_RESPONSE
            ))->responseToUser();
        } catch (\Exception $e) {
            \Log::error(__METHOD__.' => '.$e);
            return (new LineBotMessageResponser(
                $this->payload['channelId'],
                'response',
                LineBotMessageResponser::ERROR_MESSAGE
            ))->responseToUser();
        }
    }
}
