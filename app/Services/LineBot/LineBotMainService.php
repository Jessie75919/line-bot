<?php

namespace App\Services\LineBot;

use App\Services\LineBot\PushHandler\LineBotPushService;
use LINE\LINEBot;

class LineBotMainService
{
    /** @var LineBotMessageReceiver */
    private $lineBotReceiver;
    /* @var LINEBot */
    private $lineBot;

    /**
     * LineBotMainService constructor.
     */
    public function __construct()
    {
        $this->lineBot = app(LINEBot::class);
        $this->lineBotReceiver = app(LineBotMessageReceiver::class);
    }

    /**
     * @param $body
     * @param $signature
     * @return bool
     */
    public function validateSignature($body, $signature): bool
    {
        return $this->lineBot->validateSignature($body, $signature);
    }

    public function parseEventRequest($body, $signature)
    {
        return $this->lineBot->parseEventRequest($body, $signature);
    }

    public function handle($package)
    {
        $dispatchHandler = $this->lineBotReceiver->handle($package);

        if (! $dispatchHandler) {
            return null;
        }

        [$replyToken, $dataType, $channelId] = $this->getUserData();

        if ($dataType === 'location') {
            $this->lineBot->replyText($replyToken, '搜尋中...請稍等喔！');
        }

        $payload = $dispatchHandler->handle();

        if (! $payload) {
            return null;
        }

        if ($dataType === 'text') {
            return $this->lineBot->replyText($replyToken, $payload);
        }

        if ($dataType === 'location') {
            $lineBotPushService = new LineBotPushService();
            $template = $lineBotPushService->buildTemplateMessageBuilder($payload, '有訊息！請到手機上查看囉！');
            return $lineBotPushService->pushMessage($channelId, $template);
        }
    }

    /**
     * @return array
     */
    private function getUserData(): array
    {
        /** @var string $replyToken */
        $replyToken = $this->lineBotReceiver->getReplyToken();
        $dataType = $this->lineBotReceiver->getUserDataType();
        $channelId = $this->lineBotReceiver->getMemory()->channel_id;
        return [$replyToken, $dataType, $channelId];
    }
}
