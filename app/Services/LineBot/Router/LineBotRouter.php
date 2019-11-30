<?php

namespace App\Services\LineBot\Router;

use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineBot\ActionHandler\LineBotActionKeywordReplier;
use App\Services\LineBot\ActionHandler\LineBotActionLearner;
use App\Services\LineBot\ActionHandler\LineBotActionRateQuerier;
use App\Services\LineBot\ActionHandler\LineBotActionRateWatcher;
use App\Services\LineBot\ActionHandler\LineBotActionReminder;
use App\Services\LineBot\ActionHandler\LineBotActionWeightHelper;
use App\Services\LineBot\ActionHandler\LineBotCommandHelper;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Exception\InvalidEventSourceException;

class LineBotRouter
{

    public const HELP = 'help';
    public const DELIMITER_USE = ';|、|，|=';
    public const RATE = 'rate';
    public const RATE_WATCHER = 'rate_watcher';
    public const LEARN = 'learn';
    public const REMINDER = 'reminder';
    public const DELIMITER = '('.self::DELIMITER_USE.')';
    public const WEIGHT_GOAL = 'weight_goal';
    public const WEIGHT = 'weight';
    public $route = null;
    /**
     * @var Memory
     */
    private $memory;
    /**
     * @var BaseEvent
     */
    private $messageEvent;
    /**
     * @var string
     */
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
        if ($this->route) {
            return;
        }

        $memory = $this->memory;
        $text = $this->text;

        $this->route = [
            // Help指令
            [
                'pattern' => "/^help$/",
                'route' => self::HELP,
                'controller' => app(LineBotCommandHelper::class),
            ],
            // 提醒類型指令 : remRD = reminder Repeat Day \ remRW = reminder Repeat Week
            [
                'pattern' => "/^(提醒|rem|reminder|remR.*){1}\s?".self::DELIMITER."(.*)/",
                'route' => self::REMINDER,
                'controller' => app(LineBotActionReminder::class, compact('memory', 'text')),
            ],
            /* 減重小幫手 */
            [
                'pattern' => "/^(weight)".self::DELIMITER."(.*)/",
                'route' => self::WEIGHT,
                'controller' => app(LineBotActionWeightHelper::class, compact('memory', 'text')),
            ],
            /* 減重設定小幫手 */
            [
                'pattern' => "/^(weight-goal)".self::DELIMITER."(.*)/",
                'route' => self::WEIGHT_GOAL,
                'controller' => app(LineBotActionWeightHelper::class, compact('memory', 'text')),
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
                'controller' => LineBotActionRateWatcher::class, compact('memory', 'text'),
            ],
            // 學習類型指令
            [
                'pattern' => "/^(學|learn){1}\s?".self::DELIMITER."(.*)/",
                'route' => self::LEARN,
                'controller' => LineBotActionLearner::class, compact('memory', 'text'),
            ],
        ];
    }

    public function getController(): LineBotActionHandler
    {
        foreach ($this->route as $pattern) {
            if (preg_match($pattern['pattern'], $this->text) == 1) {
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