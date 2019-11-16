<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 09:47
 */

namespace App\Services\LineBot\ActionHandler;

use App\Models\Memory;
use App\Services\LineBot\TypePayloadHandler\TextTypePayloadHandler;
use LINE\LINEBot;

abstract class LineBotActionHandler
{
    protected $purpose;
    protected $channelId;
    /**
     * @var LINEBot
     */
    protected $LINEBot;

    /**
     * LineBotActionHandler constructor.
     */
    public function __construct()
    {
        $this->LINEBot = app(LINEBot::class);
    }

    /** Dissect the Message if it is a learning command with <學;key;value>
     * @param $userMessage
     * @return array
     */
    public function breakdownMessage($userMessage): array
    {
        return collect(
            preg_split(
                '/'.TextTypePayloadHandler::DELIMITER.'/',
                $userMessage
            )
        )->map(function ($item) {
            return trim($item);
        })->toArray();
    }

    abstract public function handle();

    public function reply(string $replyToken, $replyMessage)
    {
        return $this->LINEBot->replyText($replyToken, $replyMessage);
    }

    /**
     * @param  mixed  $purpose
     * @return LineBotActionHandler
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
        return $this;
    }

    /**
     * @param  mixed  $channelId
     * @return LineBotActionHandler
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
        return $this;
    }

    public function getMemory(): ?Memory
    {
        return Memory::getByChannelId($this->channelId);
    }
}
