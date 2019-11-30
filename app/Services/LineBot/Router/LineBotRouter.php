<?php

namespace App\Services\LineBot\Router;

use App\Models\Memory;
use App\Services\LineBot\ActionHandler\CurrencyRate\LineBotActionRateQuerier;
use App\Services\LineBot\ActionHandler\CurrencyRate\LineBotActionRateWatcher;
use App\Services\LineBot\ActionHandler\Help\LineBotCommandHelper;
use App\Services\LineBot\ActionHandler\Keyword\LineBotActionKeywordReplier;
use App\Services\LineBot\ActionHandler\Learner\LineBotActionLearner;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineBot\ActionHandler\Reminder\LineBotActionReminder;
use App\Services\LineBot\ActionHandler\Weight\LineBotActionWeightHelper;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Exception\InvalidEventSourceException;

class LineBotRouter
{
    const HELP = 'help';
    const DELIMITER_USE = ';|、|，|=';
    const RATE = 'rate';
    const RATE_WATCHER = 'rate_watcher';
    const LEARN = 'learn';
    const REMINDER = 'reminder';
    const DELIMITER = '('.self::DELIMITER_USE.')';
    const WEIGHT = 'weight';
    const FOOD = 'food';

    /* @var array */
    public $routes = null;
    /** @var Memory */
    private $memory;
    /** @var BaseEvent */
    private $messageEvent;
    /** @var string */
    private $text;

    /**
     * LineBotRouter constructor.
     * @param  BaseEvent  $messageEvent
     * @throws InvalidEventSourceException
     */
    public function __construct(BaseEvent $messageEvent)
    {
        $this->messageEvent = $messageEvent;
        $this->parseText()
            ->intiMemory()
            ->initRoute();
    }

    public function initRoute()
    {
        if ($this->routes) {
            return;
        }

        $memory = $this->memory;
        $text = $this->text;

        $this->routes = [
            // Help指令
            [
                'pattern' => "/^help$/",
                'route' => self::HELP,
                'controller' => app(LineBotCommandHelper::class),
            ],
            // 匯率查詢指令
            [
                'pattern' => "/^(rate)".self::DELIMITER."(.*)/",
                'route' => self::RATE,
                'controller' => app(LineBotActionRateQuerier::class, compact('text')),
            ],
            // 匯率每日通知指令
            [
                'pattern' => "/^(rate-watcher)".self::DELIMITER."(.*)/",
                'route' => self::RATE_WATCHER,
                'controller' => app(LineBotActionRateWatcher::class, compact('memory', 'text')),
            ],
            // 提醒類型指令 : remRD = reminder Repeat Day \ remRW = reminder Repeat Week
            [
                'pattern' => "/^(rem|remR.*){1}\s?".self::DELIMITER."(.*)/",
                'route' => self::REMINDER,
                'controller' => app(LineBotActionReminder::class, compact('memory', 'text')),
            ],
            /* 減重小幫手 */
            [
                'pattern' => "/^(weight.*)".self::DELIMITER."(.*)/",
                'route' => self::WEIGHT,
                'controller' => app(LineBotActionWeightHelper::class, compact('memory', 'text')),
            ],
            // 學習類型指令
            [
                'pattern' => "/^(學|learn){1}\s?".self::DELIMITER."(.*)/",
                'route' => self::LEARN,
                'controller' => app(LineBotActionLearner::class, compact('memory', 'text')),
            ],
        ];
    }

    public function getController(): ?LineBotActionHandler
    {
        foreach ($this->routes as $pattern) {
            if (preg_match($pattern['pattern'], $this->text) == 1) {
                \Log::info(__METHOD__."[".__LINE__."] => ROUTE :".$pattern['route']);
                return $pattern['controller'];
            }
        }

        return (new LineBotActionKeywordReplier($this->memory, $this->text));
    }

    private function parseText()
    {
        if ($this->messageEvent instanceof PostbackEvent) {
            $this->text = $this->messageEvent->getPostbackData();
        } else {
            $this->text = $this->messageEvent->getText();
        }
        return $this;
    }

    /**
     * @return LineBotRouter
     * @throws InvalidEventSourceException
     */
    private function intiMemory()
    {
        $channelId = $this->messageEvent->getEventSourceId();
        $this->memory = Memory::firstOrCreate(['channel_id' => $channelId], ['is_talk' => 1]);
        return $this;
    }
}