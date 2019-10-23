<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 09:47
 */

namespace App\Services\LineBot\ActionHandler;

abstract class LineBotActionHandler
{
    const DELIMITER_USE = ';|_|、|，';
    const DELIMITER = '('.self::DELIMITER_USE.')';
    protected $purpose;
    protected $channelId;

    /** Dissect the Message if it is a learning command with <學;key;value>
     * @param $userMessage
     * @return array
     */
    public static function breakdownMessage($userMessage): array
    {
        return collect(preg_split('/'.self::DELIMITER.'/', $userMessage))
            ->map(function ($item) {
                return trim($item);
            })->toArray();
    }

    abstract public function handle();

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
}
