<?php


namespace App\Services\LineBot;

use LINE\LINEBot;

class LineBotMainService
{
    /** @var LineBotMessageReceiver */
    private $lineBotReceiver;
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
        $dispatchHandler =
            $this->lineBotReceiver->handle($package);

        $responseText = $dispatchHandler->handle();

        dd($responseText);

        if (!$responseText) {
            return null;
        }

        /** @var string $replyToken */
        $replyToken = $this->lineBotReceiver->getReplyToken();

        return $this->lineBot->replyText($replyToken, $responseText);
    }
}
