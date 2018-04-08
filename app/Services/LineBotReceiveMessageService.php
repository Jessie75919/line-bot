<?php


namespace App\Services;


use App\Memory;
use function dd;
use function exp;
use function explode;
use const false;
use function substr;
use const true;

class LineBotReceiveMessageService
{
    public  $replyToken;
    public  $userMessage;
    public  $channelId;
    private $isTalk;
    private $botResponseService;
    private $botLearnService;
    private $processContent;
    private $botRemindService;
    const SPEAK            = 'speak';
    const SHUT_UP          = 'shutUp';
    const LEARN            = 'learn';
    const TALK             = 'talk';
    const RESPONSE         = 'response';
    const GENERAL_RESPONSE = '好喔～好喔～';
    const REMINDER         = 'reminder';
    const STATE            = "state";
    const REMINDER_STATE   = "Reminder-State";
    const REMINDER_DELETE  = "Reminder-Delete";


    /**
     * LineBotReceiveMessageService constructor.
     */
    public function __construct()
    {
    }


    public function handle($package): void
    {
        \Log::info('package = ' . print_r($package, true));

        // get data from user's package
        $this->getData($package);

        // for first time to use this line bot
        $this->init($this->channelId);

        // check the state of talk
        $this->isTalk = Memory::where('channel_id', $this->channelId)->first()->is_talk;

        \Log::info('channelId = ' . $this->channelId);
        \Log::info('$userMsg = ' . $this->userMessage);
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


    /**
     * @return bool
     */
    public function isTalk(): bool
    {
        return $this->isTalk;
    }


    /**
     * @param string $keyword
     * @param string $mode
     * @return bool
     */
    public function isCmd(string $keyword, string $mode): bool
    {
        $talkCmd   = ['講話', '說話', '開口'];
        $shutUpCmd = ['閉嘴', '安靜', '吵死了'];

        if(substr($keyword, 0, 2) != 'cc') {
            return false;
        }
        
        $cmd = substr($keyword, 3);
        \Log::info('cmd = ' . $cmd);

        switch($mode) {
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
     * @param string $keyWord
     * @return bool
     */
    public function isCommand($cmd, $keyWord): bool
    {
        return trim($keyWord) == $cmd ? true : false;
    }


    /** Dissect the Message if it is a learning command with <學;key;value>
     * @return array
     * @internal param $userMsg
     */
    public function dissectMessage(): array
    {
        $strArr = explode(';', $this->userMessage);

        return count($strArr) == 3
            ? $strArr
            : explode('；', $this->userMessage);

    }


    /** check the purpose of message
     * @return string
     */
    public function checkPurpose(): string
    {

        // check all to-do-list which are not done.
        $semicolons = [';','；'];
        foreach($semicolons as $semicolon) {
            $pattern = "/提醒{$semicolon}所有提醒/";
            if(preg_match($pattern, $this->userMessage) == 1) {
                return self::REMINDER_STATE;
            }
        }

        foreach($semicolons as $semicolon) {
            $pattern = "/提醒{$semicolon}刪除提醒{$semicolon}[0-9]*[1-9]*[1-9]*$/";
            if(preg_match($pattern, $this->userMessage) == 1) {
                $dissectData = $this->dissectMessage();
                $this->userMessage = $dissectData[2];
                return self::REMINDER_DELETE;
            }
        }



        $dissectData = $this->dissectMessage();

        // check need to keep to-do-list
        if($this->isCommand('提醒',$dissectData[0])) {
            $this->processContent = [
                $dissectData[1],
                $dissectData[2]
            ];
            return self::REMINDER;
        }

        if($this->isCmd($this->userMessage, self::STATE)) {
            return self::STATE;
        }

        // check need to talk
        if(!$this->isTalk()) {
            if($this->isCmd($this->userMessage, self::TALK)) {
                return self::SPEAK;
            }
            return false;
        }

        // check need to shut up
        if($this->isCmd($this->userMessage, self::SHUT_UP)) {
            return self::SHUT_UP;
        }



        // check need to learn
        if($this->isCommand('學',$dissectData[0])) {
            $this->processContent = [
                $dissectData[1],
                $dissectData[2]
            ];
            return self::LEARN;
        }




        return self::TALK;
    }


    /**
     * @param $purpose
     * @return string
     */
    public function dispatch($purpose): string
    {
        switch($purpose) {
            case self::SPEAK:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::SPEAK);
                return $this->botResponseService->responseToUser();
                break;

            case self::SHUT_UP:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::SHUT_UP);
                return $this->botResponseService->responseToUser();
                break;

            case self::TALK:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::TALK, $this->userMessage);
                return $this->botResponseService->responseToUser();
                break;

            case self::STATE:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::STATE, $this->userMessage);
                return $this->botResponseService->responseToUser();
                break;

            case self::LEARN:
                $this->botLearnService =
                    new LineBotLearnService($this->channelId, $this->processContent);
                if($this->botLearnService->learnCommand()) {
                    $this->botResponseService =
                        new LineBotResponseService($this->channelId, self::RESPONSE, self::GENERAL_RESPONSE);
                    return $this->botResponseService->responseToUser();
                }
                break;

            case self::REMINDER_STATE:
                $deleteSuccess = "你的提醒 編號：{$this->userMessage}已經被刪除囉！";
                $deleteFail    = "你的提醒 編號：{$this->userMessage}好像沒有刪除成功喔。";

                $this->botRemindService =
                    new LineBotReminderService($this->channelId, $this->userMessage);

                $result = $this->botRemindService->handle(self::REMINDER_STATE);

                $responseText = $result ? $deleteSuccess : $deleteFail;
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::RESPONSE, $responseText);
                return $this->botResponseService->responseToUser();
                break;


            case self::REMINDER_DELETE:
                $this->botRemindService =
                    new LineBotReminderService($this->channelId, $this->userMessage);

                $deleteMessage = $this->botRemindService->handle(self::REMINDER_STATE);
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::RESPONSE, $responseText);
                return $this->botResponseService->responseToUser();
                break;

            case self::REMINDER:
                $successMessage       = " 好喔～\n 我會在 [{$this->processContent[0]}] 的時候\n 提醒您 [{$this->processContent[1]}]";
                $errorMessageFormat   = " 喔 !? 輸入格式好像有點問題喔～ \n 例如：『 提醒;2018-03-04 09:30;吃早餐 』。";
                $errorMessagePastTime = " 喔 !? 輸入的時間好像有點問題。\n 請輸入『 未來 』的時間才能提醒你喔。";
                $errorMessage         = " 喔 !? 好像哪裡有點問題喔？";

                $this->botRemindService =
                    new LineBotReminderService($this->channelId, $this->processContent);

                $result = $this->botRemindService->handle(self::REMINDER);
                
                \Log::info("result => {$result}");
                
                switch($result) {
                    case 'SUCCESS':
                        $this->botResponseService =
                            new LineBotResponseService($this->channelId, self::RESPONSE, $successMessage);
                        return $this->botResponseService->responseToUser();
                        break;
                    case 'PAST_TIME_ERROR':
                        $this->botResponseService =
                            new LineBotResponseService($this->channelId, self::RESPONSE, $errorMessagePastTime);
                        return $this->botResponseService->responseToUser();
                        break;
                    case 'FORMAT_ERROR':
                        $this->botResponseService =
                            new LineBotResponseService($this->channelId, self::RESPONSE, $errorMessageFormat);
                        return $this->botResponseService->responseToUser();
                        break;
                    case 'ERROR':
                        $this->botResponseService =
                            new LineBotResponseService($this->channelId, self::RESPONSE, $errorMessage);
                        return $this->botResponseService->responseToUser();
                        break;
                }

                break;
        }
    }


    /** for the first time user which not has record in DB
     * @param $channelId
     */
    private function init($channelId): void
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if($memory) {
            return;
        }

        Memory::create([
            'channel_id' => $channelId,
            'is_talk'    => 1
        ]);
    }


    /** get data of package from user
     * @param $package
     */
    private function getData($package)
    {
//        dd($package);

        /** @var  array */
        $data = $package['events']['0'];

        /** @var  string */
        $type = $data['type'];

        /** @var array */
        $source = $data['source'];

        /** @var  array */
        $message = $data['message'];

        $this->replyToken = $data['replyToken'];

        switch($type) {
            case 'message':
                // deals with source
                switch($source['type']) {
                    case 'user':
                        $this->channelId = $source['userId'];
                        break;
                    case 'group':
                        $this->channelId = $source['groupId'];
                        break;
                    case 'room':
                        $this->channelId = $source['roomId'];
                        break;
                }

                // deals with message
                switch($message['type']) {
                    case 'text':
                        $this->userMessage = $message['text'];
                        break;
                }
                break;
        }
    }

}