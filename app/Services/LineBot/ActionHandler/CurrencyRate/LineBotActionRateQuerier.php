<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot\ActionHandler\CurrencyRate;

use App\Services\API\GuzzleApi;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use App\Services\LineExchangeRate\ExchangeRateService;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class LineBotActionRateQuerier extends LineBotActionHandler
{
    private $text;
    /**
     * @var ExchangeRateService
     */
    private $exRate;

    /**
     * LineBotActionRateWatcher constructor.
     * @param $text
     * @throws \InvalidArgumentException
     */
    public function __construct($text)
    {
        $this->exRate = new ExchangeRateService(new GuzzleApi());
        $this->text = $text;
    }

    public function handle()
    {
        try {
            $currencyChinese = $this->parseMessage($this->text)[1];
            $rate = $this->exRate
                ->setChineseCurrency($currencyChinese)
                ->fetchNowCurrencyValue()
                ->getLowest();
            return $this->reply(
                $this->exRate->toFormatCurrencyReportMessage($rate)
            );
        } catch (NotFoundResourceException $e) {
            return $this->reply(
                $this->exRate->toCurrencyNotFoundReplyMessage()
            );
        }
    }
}
