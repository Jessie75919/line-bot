<?php


namespace App\Services\LineBot;

use App\Models\Memory;
use App\Repository\LineBot\TodoListRepo;
use function substr;
use function explode;
use function preg_match;
use const true;
use const false;

class LineBotMessageReceiver
{
    const SPEAK           = 'speak';
    const SHUT_UP         = 'shutUp';
    const LEARN           = 'learn';
    const TALK            = 'talk';
    const RESPONSE        = 'response';
    const REMINDER        = 'reminder';
    const STATE           = "state";
    const REMINDER_STATE  = "Reminder-State";
    const REMINDER_DELETE = "Reminder-Delete";
    public $replyToken;
    public $userMessage;
    public $channelId;
    public $purpose;
    private $isTalk;
    /** * @var array */
    private $payload;


    /**
     * LineBotReceiveMessageService constructor.
     */
    public function __construct()
    {
    }


    public function handle($package)
    {
        \Log::info('package = ' . print_r($package, true));

        $this->initData($package)
             ->createMemory($this->channelId); // for first time to use this line bot

        // check the state of talk
        $this->isTalk = Memory::where('channel_id', $this->channelId)->first()->is_talk;

        \Log::info('channelId = ' . $this->channelId);
        \Log::info('$userMsg = ' . $this->userMessage);

        return $this->checkPurpose()
                    ->preparePayload()
                    ->dispatch();
    }


    /**
     * @return string
     */
    public function getReplyToken(): string
    {
        return $this->replyToken;
    }


    /**
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }


    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }


    /** check the purpose of message
     */
    public function checkPurpose()
    {
        // 提醒類型指令
        $pattern = "/(^提醒|reminder);(.*)/";
        if (preg_match($pattern, $this->userMessage) == 1) {
            $this->purpose = self::REMINDER;
            return $this;
        }

        // 學習類型指令
        $pattern = "/(^學|learn);(.*)/";
        if (preg_match($pattern, $this->userMessage) == 1) {
            $this->purpose = self::LEARN;
            return $this;
        }

        // 設定類型指令
        if ($this->isSettingCmd($this->userMessage, self::STATE)) {
            $this->purpose = self::STATE;
            return $this;
        }

        if (! $this->isTalk) {
            if ($this->isSettingCmd($this->userMessage, self::TALK)) {
                $this->purpose = self::SPEAK;
            }
            return $this;
        }

        if ($this->isSettingCmd($this->userMessage, self::SHUT_UP)) {
            $this->purpose = self::SHUT_UP;
            return $this;
        }

        $this->purpose = self::TALK;

        return $this;
    }


    /** Dissect the Message if it is a learning command with <學;key;value>
     * @return array
     * @internal param $userMsg
     */
    public function breakdownMessage(): array
    {
        return collect(explode(';', $this->userMessage))
            ->map(function ($item) {
                return trim($item);
            })->toArray();
    }


    /**
     * @param string $keyword
     * @param string $mode
     * @return bool
     */
    public function isSettingCmd(string $keyword, string $mode): bool
    {
        $talkCmd = ['講話', '說話', '開口'];
        $shutUpCmd = ['閉嘴', '安靜', '吵死了'];
        $stateCmd = ['狀態'];

        if (substr($keyword, 0, 2) != 'jc') {
            return false;
        }

        $cmd = explode(' ', $keyword)[1];

        switch ($mode) {
            case self::TALK:
                return in_array($cmd, $talkCmd);
            case self::SHUT_UP:
                return in_array($cmd, $shutUpCmd);
            case self::STATE:
                return in_array($cmd, $stateCmd);
        }
    }


    public function preparePayload()
    {
        $breakdownMessage = $this->breakdownMessage();

        $this->payload = [
            'channelId' => $this->channelId,
            'purpose'   => $this->purpose,
            'message'   => [
                'origin' => $this->userMessage,
                'key'    => $this->isCommonPurpose($this->purpose)
                    ? null
                    : count($breakdownMessage) > 0 ? $breakdownMessage[1] : null,
                'value'  => $this->isCommonPurpose($this->purpose)
                    ? null
                    : count($breakdownMessage) === 3 ? $breakdownMessage[2] : null,
            ]
        ];

        return $this;
    }


    public function dispatch(): LineBotActionHandlerInterface
    {
        switch ($this->purpose) {
            case $this->isCommonPurpose($this->purpose):
                return new LineBotActionCommonReplier($this->payload);

            case self::LEARN:
                return new LineBotActionLearner($this->payload);
                break;

            case self::REMINDER:
                $todoListRepo = app(TodoListRepo::class);
                return new LineBotActionReminder($this->payload, $todoListRepo);
        }
    }


    /** get data of package from user
     * @param $package
     * @return LineBotMessageReceiver
     */
    private function initData($package)
    {
        /** @var  array */
        $data = $package['events']['0'];

        /** @var  string */
        $type = $data['type'];

        /** @var array */
        $source = $data['source'];

        /** @var  array */
        $message = $data['message'];

        $this->replyToken = $data['replyToken'];

        switch ($type) {
            case 'message':
                // deals with source
                $sourceType = $source['type'];
                $this->channelId = $source["{$sourceType}Id"];

                // deals with message
                switch ($message['type']) {
                    case 'text':
                        $this->userMessage = $message['text'];
                        break;
                }
                break;
        }

        return $this;
    }


    /** for the first time user which not has record in DB
     * @param $channelId
     * @return LineBotMessageReceiver
     */
    private function createMemory($channelId)
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if ($memory) {
            return $this;
        }

        Memory::create([
            'channel_id' => $channelId,
            'is_talk'    => 1
        ]);

        return $this;
    }


    /**
     * @param $purpose
     * @return bool
     */
    private function isCommonPurpose($purpose): bool
    {
        return self::SPEAK === $purpose ||
            self::SHUT_UP === $purpose ||
            self::TALK === $purpose ||
            self::STATE === $purpose;
    }
}
