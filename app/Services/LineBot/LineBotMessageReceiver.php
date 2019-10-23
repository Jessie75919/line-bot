<?php

namespace App\Services\LineBot;

use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineBot\TypePayloadHandler\LocationTypePayloadHandler;
use App\Services\LineBot\TypePayloadHandler\TextTypePayloadHandler;

class LineBotMessageReceiver
{
    public $replyToken;
    public $userData;
    public $channelId;
    public $purpose;
    /** * @var string */
    private $userDataType;
    private $memory;

    public function handle($package)
    {
        \Log::info('package = '.print_r($package, true));

        if (! $this->initData($package)) {
            return null;
        }

        \Log::info('channelId = '.$this->channelId);

        return $this->createMemory($this->channelId)
            ->dispatchByPayloadType();
    }

    /**
     * @return string
     */
    public function getReplyToken(): string
    {
        return $this->replyToken;
    }

    public function dispatchByPayloadType(): LineBotActionHandler
    {
        if ($this->userDataType === 'text') {
            return (new TextTypePayloadHandler($this->memory))
                ->checkPurpose($this->userData)
                ->dispatch();
        }

        if ($this->userDataType === 'location') {
            return (new LocationTypePayloadHandler($this->memory))
                ->checkPurpose($this->userData)
                ->preparePayload()
                ->dispatch();
        }
    }

    /**
     * @return string
     */
    public function getUserDataType(): string
    {
        return $this->userDataType;
    }

    /**
     * @return mixed
     */
    public function getMemory()
    {
        return $this->memory;
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
                        $this->userData = $message['text'];
                        $this->userDataType = 'text';
                        return $this;
                    case 'location':
                        $this->userDataType = 'location';
                        $this->userData = [
                            'address' => $message['address'],
                            'latitude' => $message['latitude'],
                            'longitude' => $message['longitude'],
                        ];
                        return $this;
                }
                break;
        }

        return null;
    }

    /** for the first time user which not has record in DB
     * @param $channelId
     * @return LineBotMessageReceiver
     */
    private function createMemory($channelId)
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if (! $memory) {
            $memory = Memory::create([
                'channel_id' => $channelId,
                'is_talk' => 1,
            ]);
        }

        $this->memory = $memory;
        return $this;
    }
}
