<?php

namespace App\Services;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Response;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineBotService
{
    /** @var LINEBot */
    private $lineBot;
    /** @var string */
    private $lineUserId;


    public function __construct($lineUserId)
    {
        $this->lineBot    = app(LINEBot::class);
        $this->lineUserId = $lineUserId;
    }


    /**
     * @param TemplateMessageBuilder|string $content
     * @return Response
     */
    public function pushMessage($content): Response
    {
        if(is_string($content)) {
            $content = new TextMessageBuilder($content);
        }
        return $this->lineBot->pushMessage($this->lineUserId, $content);
    }


    /**
     * @param string $imagePath
     * @param string $directUri
     * @param string $label
     * @return TemplateMessageBuilder
     */
    public function buildTemplateMessageBuilder(
        string $imagePath,
        string $directUri,
        string $label
    ): TemplateMessageBuilder {
        $action = new UriTemplateActionBuilder($label, $directUri);
        $target = new ImageCarouselColumnTemplateBuilder($imagePath, $action);

        return new TemplateMessageBuilder('WTF', $target);
    }


}