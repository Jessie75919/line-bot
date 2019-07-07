<?php


namespace App\Services\LineBot;


use App\Models\Memory;
use function substr;
use function explode;
use function preg_match;
use const true;
use const false;

class LineBotMessageReceiver
{
    const SPEAK            = 'speak';
    const SHUT_UP          = 'shutUp';
    const LEARN            = 'learn';
    const TALK             = 'talk';
    const RESPONSE         = 'response';
    const GENERAL_RESPONSE = '好喔～好喔～';
    const REMINDER         = 'notice';
    const STATE            = "state";
    const REMINDER_STATE   = "Reminder-State";
    const REMINDER_DELETE  = "Reminder-Delete";
    public  $replyToken;
    public  $userMessage;
    public  $channelId;
    public  $purpose;
    private $isTalk;
    private $botResponseService;
    private $botLearnService;
    private $processContent;
    private $botRemindService;


    /**
     * LineBotReceiveMessageService constructor.
     */
    public function __construct ()
    {
    }


    public function handle ($package)
    {
        \Log::info('package = ' . print_r($package, true));

        // get data from user's package
        $this->initData($package);

        // for first time to use this line bot
        $this->createMemory($this->channelId);

        // check the state of talk
        $this->isTalk = Memory::where('channel_id', $this->channelId)->first()->is_talk;

        \Log::info('channelId = ' . $this->channelId);
        \Log::info('$userMsg = ' . $this->userMessage);

        return $this;
    }


    /** get data of package from user
     * @param $package
     */
    private function initData ($package)
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
                $sourceType      = $source['type'];
                $this->channelId = $source["{$sourceType}Id"];

                // deals with message
                switch ($message['type']) {
                    case 'text':
                        $this->userMessage = $message['text'];
                        break;
                }
                break;
        }
    }


    /** for the first time user which not has record in DB
     * @param $channelId
     */
    private function createMemory ($channelId): void
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if ($memory) {
            return;
        }

        Memory::create([
            'channel_id' => $channelId,
            'is_talk'    => 1
        ]);
    }


    /**
     * @return string
     */
    public function getReplyToken (): string
    {
        return $this->replyToken;
    }


    /**
     * @return string
     */
    public function getUserMessage (): string
    {
        return $this->userMessage;
    }


    /**
     * @return string
     */
    public function getChannelId (): string
    {
        return $this->channelId;
    }


    /** check the purpose of message
     */
    public function checkPurpose ()
    {
        $breakdownMessage = $this->breakdownMessage();

        // 提醒類型指令
        $pattern = "/(^提醒);(.*)/";
        if (preg_match($pattern, $this->userMessage) == 1) {
            $purpose = trim($breakdownMessage[1]);
            switch ($purpose) {
                case 'all' || '所有提醒':
                    $this->purpose = self::REMINDER_STATE;
                    break;
                case '刪除' || 'del':
                    $this->purpose = self::REMINDER_DELETE;
                    break;
                default:
                    $this->purpose = self::REMINDER;
            }
        }

        // 學習類型指令
        $pattern = "/(^學);(.*)/";
        if (preg_match($pattern, $this->userMessage) == 1) {
            $this->purpose = self::LEARN;
        }

        // 設定類型指令
        if ($this->isSettingCmd($this->userMessage, self::STATE)) {
            $this->purpose = self::STATE;
        }

        if (! $this->isTalk()) {
            if ($this->isSettingCmd($this->userMessage, self::TALK)) {
                $this->purpose = self::SPEAK;
            }
            return false;
        }

        if ($this->isSettingCmd($this->userMessage, self::SHUT_UP)) {
            $this->purpose = self::SHUT_UP;
        }

        $this->purpose = self::TALK;

        return $this;
    }


    /** Dissect the Message if it is a learning command with <學;key;value>
     * @return array
     * @internal param $userMsg
     */
    public function breakdownMessage (): array
    {
        return explode(';', $this->userMessage);
    }


    /**
     * @param string $keyword
     * @param string $mode
     * @return bool
     */
    public function isSettingCmd (string $keyword, string $mode): bool
    {
        $talkCmd   = ['講話', '說話', '開口'];
        $shutUpCmd = ['閉嘴', '安靜', '吵死了'];

        if (substr($keyword, 0, 2) != 'cc') {
            return false;
        }

        $cmd = substr($keyword, 3);
        \Log::info('cmd = ' . $cmd);

        switch ($mode) {
            case self::TALK:
                return in_array($cmd, $talkCmd);
                break;
            case self::SHUT_UP:
                return in_array($cmd, $shutUpCmd);
                break;
            case self::STATE:
                return $cmd == '狀態';
        }
    }


    /**
     * @return bool
     */
    public function isTalk (): bool
    {
        return $this->isTalk;
    }


    /**
     * @return string
     */
    public function dispatch (): string
    {
        switch ($this->purpose) {
            case self::SPEAK || self::SHUT_UP || self::TALK || self::STATE:
                return (new LineBotMessageResponser($this->channelId, $this->purpose))->responseToUser();

            case self::LEARN:
                $this->botLearnService = new LineBotLearner($this->channelId, $this->processContent);
                if ($this->botLearnService->learnCommand()) {
                    $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE,
                        self::GENERAL_RESPONSE);
                    return $this->botResponseService->responseToUser();
                }
                break;

            case self::REMINDER_STATE:
                $this->botRemindService = new LineBotReminder($this->channelId, $this->userMessage);
                $responseText           = $this->botRemindService->handle(self::REMINDER_STATE);

                $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE, $responseText);
                return $this->botResponseService->responseToUser();


            case self::REMINDER_DELETE:
                $deleteSuccess = "你的提醒編號：{$this->userMessage}已經被刪除囉！";
                $deleteFail    = "你的提醒編號：{$this->userMessage}好像沒有刪除成功喔。";

                $this->botRemindService = new LineBotReminder($this->channelId, $this->userMessage);

                $result       = $this->botRemindService->handle(self::REMINDER_DELETE);
                $responseText = $result ? $deleteSuccess : $deleteFail;

                $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE, $responseText);
                return $this->botResponseService->responseToUser();

            case self::REMINDER:
                $this->botRemindService = new LineBotReminder($this->channelId, $this->processContent);

                list($result, $responseText) = $this->botRemindService->handle(self::REMINDER);

                $successMessage       = " [提醒時間]\n {$responseText}\n============= \n [提醒內容]\n {$this->processContent[1]}";
                $errorMessageFormat   = " 喔 !? 輸入格式好像有點問題喔～ \n 例如：『 提醒;2018-03-04 09:30;吃早餐 』。";
                $errorMessagePastTime = " 喔 !? 輸入的時間好像有點問題。\n 請輸入『 未來 』的時間才能提醒你喔。";
                $errorMessage         = " 喔 !? 好像哪裡有點問題喔？";

                \Log::info("result => {$result}");

                switch ($result) {
                    case 'SUCCESS':
                        $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE,
                            $successMessage);
                        return $this->botResponseService->responseToUser();
                        break;
                    case 'PAST_TIME_ERROR':
                        $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE,
                            $errorMessagePastTime);
                        return $this->botResponseService->responseToUser();
                        break;
                    case 'FORMAT_ERROR':
                        $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE,
                            $errorMessageFormat);
                        return $this->botResponseService->responseToUser();
                        break;
                    case 'ERROR':
                        $this->botResponseService = new LineBotMessageResponser($this->channelId, self::RESPONSE,
                            $errorMessage);
                        return $this->botResponseService->responseToUser();
                        break;
                }
        }
    }

}