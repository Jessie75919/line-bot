<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot\ActionHandler\CurrencyRate;

use App\Models\Memory;
use App\Services\API\GuzzleApi;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineExchangeRate\ExchangeRateService;

class LineBotActionRateWatcher extends LineBotActionHandler
{
    private $exRate;

    /**
     * LineBotActionRateWatcher constructor.
     * @param  Memory  $memory
     * @param $message
     * @throws \InvalidArgumentException
     */
    public function __construct(Memory $memory, $message)
    {
        parent::__construct($memory, $message);
        $this->exRate = new ExchangeRateService(new GuzzleApi());
    }

    public function handle()
    {
        [$currency, $type] = $this->getCurrencyAndType($this->message);

        $message = $this->exRate
            ->subscribe($this->memory, $currency, $type);

        return $this->reply($message);
    }

    public function getCurrencyAndType($message)
    {
        $msgArr = $this->parseMessage($message);
        /* 日幣＼美金＼澳幣 */
        $currency = $msgArr[1];
        /* 現金＼即期 */
        $type = $msgArr[2] ?? '現金';

        return [$currency, $type];
    }
}
