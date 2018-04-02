<?php

namespace App\Services;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Response;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

/** This is a app entry point
 * Class LineBotService
 * @package App\Services
 */
class LineBotPushService
{
    /** @var LINEBot */
    private $lineBot;
    /** @var string */
    private $lineUserId;


    public function __construct()
    {
        $this->lineBot = app(LINEBot::class);
    }


    /**
     * @param                               $channelId
     * @param TemplateMessageBuilder|string $content
     * @return Response
     */
    public function pushMessage($channelId, $content): Response
    {
        if(is_string($content)) {
            $content = new TextMessageBuilder($content);
        }
        return $this->lineBot->pushMessage($channelId, $content);
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
        $img    = new ImageCarouselColumnTemplateBuilder($imagePath, $action);

        $target = new ImageCarouselTemplateBuilder([$img, $img]);
        return new TemplateMessageBuilder('WTF', $target);
    }


    /**
     * @param string $lineUserId
     */
    public function setLineUserId(string $lineUserId)
    {
        $this->lineUserId = $lineUserId;
    }


}