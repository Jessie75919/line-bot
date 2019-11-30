<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot\ActionHandler;

use App\Models\Memory;
use App\Services\API\GuzzleApi;
use App\Services\LineExchangeRate\ExchangeRateService;

class LineBotActionRateWatcher extends LineBotActionHandler
{
    private $exRate;
    /**
     * @var Memory
     */
    private $memory;
    private $text;

    /**
     * LineBotActionRateWatcher constructor.
     * @param  Memory  $memory
     * @param $text
     * @throws \InvalidArgumentException
     */
    public function __construct(Memory $memory, $text)
    {
        $this->exRate = new ExchangeRateService(new GuzzleApi());
        $this->memory = $memory;
        $this->text = $text;
    }

    public function handle()
    {
        [$currency, $type] = $this->parseMessage($this->text);
        return $this->exRate
            ->subscribe($this->memory, $currency, $type);
    }

    private function parseMessage($message)
    {
        $msgArr = $this->breakdownMessage($message);
        /* 日幣＼美金＼澳幣 */
        $currency = $msgArr[1];
        /* 現金＼即期 */
        $type = $msgArr[2] ?? '現金';

        return [$currency, $type];
    }
}
