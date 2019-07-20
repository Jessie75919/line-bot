<?php


namespace App\Services\LineBot;

use LINE\LINEBot;
use App\Services\LineBot\PushHandler\LineBotPushService;

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


    public function handle($package)
    {
        $dispatchHandler = $this->lineBotReceiver->handle($package);

        if (! $dispatchHandler) {
            return null;
        }

        $payload = $dispatchHandler->handle();

        if (! $payload) {
            return null;
        }

        $dataType = $this->lineBotReceiver->getUserDataType();

        if ($dataType === 'text') {
            /** @var string $replyToken */
            $replyToken = $this->lineBotReceiver->getReplyToken();
            return $this->lineBot->replyText($replyToken, $payload);
        }

        if ($dataType === 'location') {
            $lineBotPushService = new LineBotPushService();
            
            $template = $lineBotPushService->buildTemplateMessageBuilder($payload, '有訊息！請到手機上查看囉！');

            $channelId = $this->lineBotReceiver->getMemory()->channel_id;

            return  $lineBotPushService->pushMessage($channelId, $template);
        }
    }
}
