<?php
namespace App\Service;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotService
{
    private $lineBot;
    private $lineUserId;


    public function __construct($lineUserId)
    {
        $this->lineBot    = app(LINEBot::class);
        $this->lineUserId = $lineUserId;
    }

    public function fake()
    {
    }

    /**
     * @param TemplateMessageBuilder|string $content
     * @return Response
     */
    public function pushMessage($content): Response
    {
        if (is_string($content)) {
            $content = new TextMessageBuilder($content);
        }
        return $this->lineBot->pushMessage($this->lineUserId, $content);
    }

    
}