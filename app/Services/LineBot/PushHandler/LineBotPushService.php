<?php

namespace App\Services\LineBot\PushHandler;

use Illuminate\Support\Collection;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
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
     * @param  TemplateMessageBuilder|string  $content
     * @return Response
     */
    public function pushMessage($channelId, $content): Response
    {
        if (is_string($content)) {
            $content = new TextMessageBuilder($content);
        }
        return $this->lineBot->pushMessage($channelId, $content);
    }

    /**
     * @param  Collection  $payload
     * @param  string  $altText
     * @return TemplateMessageBuilder
     */
    public function buildTemplateMessageBuilder(Collection $payload, $altText = ''): TemplateMessageBuilder
    {
        $columnTemplateBuilders = [];

        foreach ($payload as $item) {
            $urlActions = [];

            if ($item->website) {
                $urlActions[] = new UriTemplateActionBuilder('Google 地圖查看', $item->url);
                $urlActions[] = new UriTemplateActionBuilder('前往官網', $item->website);
            } else {
                $urlActions[] = new UriTemplateActionBuilder('Google 地圖查看', $item->url);
                $urlActions[] = new UriTemplateActionBuilder('Google 地圖查看', $item->url);
            }

            $columnTemplateBuilders[] = new CarouselColumnTemplateBuilder(
                $item->label,
                $item->is_opening,
                $item->photo_url,
                $urlActions
            );

        }

        $target = new CarouselTemplateBuilder($columnTemplateBuilders);
        return new TemplateMessageBuilder($altText, $target);
    }

    /**
     * @param  string  $lineUserId
     */
    public function setLineUserId(string $lineUserId)
    {
        $this->lineUserId = $lineUserId;
    }

    /**
     * @return LINEBot
     */
    public function getLineBot(): LINEBot
    {
        return $this->lineBot;
    }
}
