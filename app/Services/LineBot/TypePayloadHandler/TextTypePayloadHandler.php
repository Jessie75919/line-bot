<?php

namespace App\Services\LineBot\TypePayloadHandler;

use App\Repository\LineBot\TodoListRepo;
use App\Services\LineBot\ActionHandler\LineBotActionCommonReplier;
use App\Services\LineBot\ActionHandler\LineBotActionLearner;
use App\Services\LineBot\ActionHandler\LineBotActionRateQuerier;
use App\Services\LineBot\ActionHandler\LineBotActionRateWatcher;
use App\Services\LineBot\ActionHandler\LineBotActionReminder;
use App\Services\LineBot\ActionHandler\LineBotActionWeightHelper;
use App\Services\LineBot\ActionHandler\LineBotCommandHelper;
use LINE\LINEBot\Event\MessageEvent;

class TextTypePayloadHandler implements TypePayloadHandlerInterface
{
    /* Commands */
    const LEARN = 'learn';
    const RATE = 'rate';
    const TALK = 'talk';
    const HELP = 'help';
    const WEIGHT = 'weight';
    const WEIGHT_GOAL = 'weight_goal';
    const RESPONSE = 'response';
    const REMINDER = 'reminder';
    const RATE_WATCHER = 'rate_watcher';

    public const DELIMITER_USE = ';|、|，';
    public const DELIMITER = '('.self::DELIMITER_USE.')';

    private $memory;
    private $rawPayload;
    private $payload;
    private $purpose;

    /**
     * TextPurposeChecker constructor.
     * @param $memory
     */
    public function __construct($memory)
    {
        $this->memory = $memory;
    }

    public function checkPurpose(MessageEvent $textMessage)
    {
        $this->rawPayload = $textMessage->getText();
        $text = $textMessage->getText();

        // Help指令
        if (strtolower($text) === self::HELP) {
            $this->purpose = self::HELP;
            return $this;
        }

        // 提醒類型指令 : remRD = reminder Repeat Day \ remRW = reminder Repeat Week
        $pattern = "/^(提醒|rem|reminder|remR.*){1}\s?".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $text) == 1) {
            $this->purpose = self::REMINDER;
            return $this;
        }

        /* 減重小幫手 */
        $pattern = "/^(體重|weight|body)".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $text) == 1) {
            $this->purpose = self::WEIGHT;
            return $this;
        }

        /* 減重設定小幫手 */
        $pattern = "/^(weight-goal)".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $text) == 1) {
            $this->purpose = self::WEIGHT_GOAL;
            return $this;
        }

        // 匯率查詢指令
        $pattern = "/^(匯率|rate)".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $text) == 1) {
            $this->purpose = self::RATE;
            return $this;
        }

        // 匯率每日通知指令
        $pattern = "/^(rate-watcher)".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $text) == 1) {
            $this->purpose = self::RATE_WATCHER;
            return $this;
        }

        // 學習類型指令
        $pattern = "/^(學|learn){1}\s?".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $text) == 1) {
            $this->purpose = self::LEARN;
            return $this;
        }

        $this->purpose = self::TALK;

        return $this;
    }

    public function dispatch()
    {
        switch ($this->purpose) {
            case self::TALK:
                $instance = new LineBotActionCommonReplier();
                break;
            case self::HELP:
                $instance = new LineBotCommandHelper();
                break;
            case self::RATE:
                $instance = new LineBotActionRateQuerier();
                break;
            case self::WEIGHT || self::WEIGHT_GOAL:
                $instance = new LineBotActionWeightHelper();
                break;
            case self::RATE_WATCHER:
                $instance = new LineBotActionRateWatcher();
                break;
            case self::LEARN:
                $instance = new LineBotActionLearner();
                break;
            case self::REMINDER:
                $todoListRepo = app(TodoListRepo::class);
                $instance = new LineBotActionReminder($todoListRepo);
                break;
            default:
                $this->purpose = self::HELP;
                $instance = new LineBotCommandHelper();
        }

        return $instance
            ->setChannelId($this->memory->channel_id)
            ->setPurpose($this->purpose)
            ->preparePayload($this->rawPayload);
    }
}
