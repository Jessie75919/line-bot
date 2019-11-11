<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot\ActionHandler;

use App\Services\API\GuzzleApi;
use App\Services\LineExchangeRate\ExchangeRateService;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class LineBotActionRateQuerier extends LineBotActionHandler
{
    private $payload;
    /**
     * @var ExchangeRateService
     */
    private $exRate;
    private $currentChineseStr;

    /**
     * LineBotActionRateWatcher constructor.
     */
    public function __construct()
    {
        $this->exRate = new ExchangeRateService(new GuzzleApi());
    }

    public function preparePayload($rawPayload)
    {
        $msgArr = $this->breakdownMessage($rawPayload);

        $this->currentChineseStr = $msgArr[1];

        return $this;
    }

    public function handle()
    {
        try {
            $rate = $this->exRate
                ->setChineseCurrency($this->currentChineseStr)
                ->fetchNowCurrencyValue()
                ->getLowest();
            return $this->exRate->toFormatCurrencyReportMessage($rate);
        } catch (NotFoundResourceException $e) {
            return $this->exRate->toCurrencyNotFoundReplyMessage();
        }
    }
}
