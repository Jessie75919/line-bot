<?php


namespace App\Services;


use App\Memory;

class LineBotReceiveMessageService
{
    public  $replyToken;
    public  $userMessage;
    public  $channelId;
    private $isTalk;
    private $botResponseService;
    private $botLearnService;
    private $learnContent;
    const SPEAK            = 'speak';
    const SHUT_UP          = 'shutUp';
    const LEARN            = 'learn';
    const TALK             = 'talk';
    const RESPONSE         = 'response';
    const GENERAL_RESPONSE = '好喔～好喔～';


    /**
     * LineBotReceiveMessageService constructor.
     * @param $package
     * @internal param $replyToken
     * @internal param $channelId
     */
    public function __construct($package)
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
    public function isNeed(string $keyword, string $mode): bool
    {
        $talkCmd   = ['講話', '說話', '開口'];
        $shutUpCmd = ['閉嘴', '安靜', '吵死了'];

        $cmd = substr($keyword, 3);
        \Log::info('cmd = ' . $cmd);

        switch($mode) {
            case self::TALK:
                return in_array($cmd, $talkCmd);
                break;
            case self::SHUT_UP:
                return in_array($cmd, $shutUpCmd);
                break;
        }
    }


    /**
     * @param string $learnWord
     * @return bool
     */
    public function isLearningCommand($learnWord): bool
    {
        return trim($learnWord) == '學' ? true : false;
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
        // check need to talk
        if(!$this->isTalk()) {
            if($this->isNeed($this->userMessage, self::TALK)) {
                return self::SPEAK;
            }
            return false;
        }

        // check need to shut up
        if($this->isNeed($this->userMessage, self::SHUT_UP)) {
            return self::SHUT_UP;
        }

        $dissectData = $this->dissectMessage();

        // check need to learn
        if($this->isLearningCommand($dissectData[0])) {
            $this->learnContent = [
                'key'     => $dissectData[1],
                'message' => $dissectData[2]
            ];
            return self::LEARN;
        }
        return self::TALK;
    }


    /**
     * @param $purpose
     * @return string
     */
    public function dispatch($purpose):string
    {
        switch($purpose) {
            case self::SPEAK:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::SPEAK);
                return $this->botResponseService->responsePurpose();
                break;
            case self::SHUT_UP:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::SHUT_UP);
                return $this->botResponseService->responsePurpose();
                break;
            case self::TALK:
                $this->botResponseService =
                    new LineBotResponseService($this->channelId, self::TALK, $this->userMessage);
                return $this->botResponseService->responsePurpose();
                break;

            case self::LEARN:
                $this->botLearnService =
                    new LineBotLearnService($this->channelId, $this->learnContent);
                if($this->botLearnService->learnCommand()) {
                    $this->botResponseService =
                        new LineBotResponseService($this->channelId, self::RESPONSE, self::GENERAL_RESPONSE);
                    return $this->botResponseService->responsePurpose();
                }
                break;
        }
    }


    /** for the first time user which not has record in DB
     * @param $channelId
     */
    private function init($channelId):void
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if($memory) { return; }

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