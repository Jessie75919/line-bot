<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 09:47
 */

namespace App\Services\LineBot\ActionHandler;

use App\Services\LineBot\Router\LineBotRouter;
use LINE\LINEBot;

abstract class LineBotActionHandler
{
    protected $replyToken;

    /** Dissect the Message if it is a learning command with <學;key;value>
     * @param $userMessage
     * @return array
     */
    public function parseMessage($userMessage): array
    {
        return collect(
            preg_split(
                '/'.LineBotRouter::DELIMITER.'/',
                $userMessage
            )
        )->map(function ($item) {
            return trim($item);
        })->toArray();
    }

    /**
     * @param $message
     * @return mixed
     */
    public function reply($message)
    {
        return app(LINEBot::class)
            ->replyText($this->replyToken, $message);
    }

    abstract public function handle();

    /**
     * @param  mixed  $replyToken
     * @return LineBotActionHandler
     */
    public function setReplyToken($replyToken)
    {
        $this->replyToken = $replyToken;
        return $this;
    }
}
