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

class LineBotActionLearner implements LineBotActionHandlerInterface
{
    private $payload;


    /**
     * LineBotLearnService constructor.
     * @param $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }


    public function handle()
    {
        $key = $this->payload['message']['key'];
        $message = $this->payload['message']['value'];

        \Log::info('key = ' . $key);
        \Log::info('message = ' . $message);

        if (strlen($key) <= 0 && strlen($message) <= 0) {
            return false;
        }

        try {
            Message::create(
                [
                    'keyword'    => $key,
                    'message'    => $message,
                    'channel_id' => $this->payload['channelId']
                ]
            );

            return (new LineBotMessageResponser(
                $this->payload['channelId'],
                'response',
                LineBotMessageResponser::GENERAL_RESPONSE
            ))->responseToUser();
        } catch (\Exception $e) {
            \Log::error(__METHOD__ . ' => ' . $e);
            return (new LineBotMessageResponser(
                $this->payload['channelId'],
                'response',
                LineBotMessageResponser::ERROR_MESSAGE
            ))->responseToUser();
        }
    }
}
