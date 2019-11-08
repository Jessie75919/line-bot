<?php

namespace App\Services\LineBot\TypePayloadHandler;

use App\Repository\LineBot\TodoListRepo;
use App\Services\LineBot\ActionHandler\LineBotActionCommonReplier;
use App\Services\LineBot\ActionHandler\LineBotActionLearner;
use App\Services\LineBot\ActionHandler\LineBotActionRateWatcher;
use App\Services\LineBot\ActionHandler\LineBotActionReminder;
use App\Services\LineBot\ActionHandler\LineBotCommandHelper;

class TextTypePayloadHandler implements TypePayloadHandlerInterface
{
    const LEARN = 'learn';
    const RATE = 'rate';
    const TALK = 'talk';
    const HELP = 'help';
    const RESPONSE = 'response';
    const REMINDER = 'reminder';
    public const DELIMITER_USE = ';|_|、|，';
    public const DELIMITER = '('.TextTypePayloadHandler::DELIMITER_USE.')';

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

    /**
     * @param $purpose
     * @return bool
     */
    public function isCommonPurpose($purpose): bool
    {
        return self::SPEAK === $purpose ||
            self::SHUT_UP === $purpose ||
            self::TALK === $purpose ||
            self::STATE === $purpose ||
            self::HELP === $purpose;
    }

    public function checkPurpose($textPayload)
    {
        $this->rawPayload = $textPayload;

        // Help指令
        if (strtolower($textPayload) === 'help') {
            $this->purpose = self::HELP;
            return $this;
        }

        // 提醒類型指令 : remRD = reminder Repeat Day \ remRW = reminder Repeat Week
        $pattern = "/^(提醒|rem|reminder|remR.*){1}\s?".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $textPayload) == 1) {
            $this->purpose = self::REMINDER;
            return $this;
        }

        // 匯率查詢指令
        $pattern = "/^(匯率|rate)".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $textPayload) == 1) {
            $this->purpose = self::RATE;
            return $this;
        }

        // 學習類型指令
        $pattern = "/^(學|learn){1}\s?".self::DELIMITER."(.*)/";
        if (preg_match($pattern, $textPayload) == 1) {
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
